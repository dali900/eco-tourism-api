<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attraction\AttractionCategoryCreateRequest;
use App\Http\Requests\Attraction\AttractionCategoryUpdateRequest;
use App\Http\Resources\Attraction\AttractionCategoryResource;
use App\Http\Resources\Attraction\AttractionCategoryResourcePaginated;
use App\Http\Resources\Attraction\AttractionResource;
use App\Http\Resources\Attraction\AttractionResourcePaginated;
use App\Http\Resources\Trip\TripResource;
use App\Models\Attraction;
use App\Models\AttractionCategory;
use App\Models\Language;
use App\Models\Translations\AttractionCategoryTranslation;
use App\Models\Trip;
use App\Repositories\AttractionCategoryRepository;
use App\Repositories\AttractionRepository;
use Illuminate\Http\Request;

class AttractionCategoryController extends Controller
{
    /**
     * AttractionCategoryRepository
     *
     * @var AttractionCategoryRepository
     */
    
    private $attractionCategoryRepository;

    /**
     * AttractionRepository
     *
     * @var AttractionRepository
     */
    private $attractionRepository;

    public function __construct(AttractionCategoryRepository $attractionCategoryRepository, AttractionRepository $attractionRepository) {
        $this->attractionCategoryRepository = $attractionCategoryRepository;
        $this->attractionRepository = $attractionRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 20;
        $categories = $this->attractionCategoryRepository->getAllFiltered($request->all());
        //$regulationTypes->groupBy('name')->select(DB::raw("max(id),max(created_at)"));
        $categories->with('parent');
        $categoriesPaginated = $categories->paginate($perPage);
        return $this->responseSuccess(AttractionCategoryResourcePaginated::make($categoriesPaginated));
    }

    /**
     * Display the specified resource.
     */
    public function get(string $id, string $langId = null)
    {
        $langId = getSelectedOrDefaultLangId($langId);
        $attractionCategory = AttractionCategory::with([
                'translation' => fn ($query) => $query->where('language_id', $langId)
            ])
            ->find($id);
        if(!$attractionCategory){
            return $this->responseNotFound();
        }
        $attractionCategory->translateModelByLangId($langId);

        return $this->responseSuccess(AttractionCategoryResource::make($attractionCategory));
    }

    /**
     * Display the specified resource.
     */
    public function adminGet(string $id, string $langId = null)
    {
        $langId = getSelectedOrDefaultLangId($langId);
        $attractionCategory = AttractionCategory::with([
                'translations',
                'translation' => fn ($query) => $query->where('language_id', $langId)
            ])
            ->find($id);
        if(!$attractionCategory){
            return $this->responseNotFound();
        }

        if ($langId) {
            if (!$attractionCategory->translateModelByLangId($langId)) {
                $attractionCategory->emptyModel(new AttractionCategoryTranslation());
            } 
        } else {
            $attractionCategory->translateModelByLangCode(Language::SR_CODE);
        }

        return $this->responseSuccess(AttractionCategoryResource::make($attractionCategory));
    }

    /**
     * Display a listing of the resource.
     */
    public function getCatagoryAttractions(Request $request, $id)
    {
        $langId = getLnaguageId($request);
        $perPage = $request->perPage ?? 20;
        $category = AttractionCategory::with([
            'translation' => fn ($query) => $query->where('language_id', $langId)
        ])
        ->find($id);

        if (!$category) {
            return $this->responseNotFound();
        }

        $categoriesIds = $category->descendantsAndSelf()->get()->pluck('id')->all();
        $filters = $request->all();
        if ($categoriesIds) {
            $filters['category_ids'] = $categoriesIds;
        }
        
        $attractions = Attraction::with([
            'category',
            'thumbnail',
            'translation' => fn ($query) => $query->where('language_id', $langId),
        ]);
        $attractions = $this->attractionRepository->getAllFiltered($filters, $attractions);
        $attractionsPaginated = $attractions->paginate($perPage);
        
        $attractionResource = AttractionResourcePaginated::make($attractionsPaginated);
        $categoryResource = AttractionCategoryResource::make($category);
        return $this->responseSuccess([
            'category' => $categoryResource,
            'attractions' => $attractionResource
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getRoots(Request $request)
    {
        //Can't apply constraint (limit 3) after eager loading relation, for each relation. (works only for the first relation)
        //Solution: go through each category and use the load()
        $categories = AttractionCategory::whereNull('parent_id')->get();
        $categories->each(function($category) {
            $category->load([
                'attractions' => fn($q) => $q->limit(3)
            ]);
        });
        return $this->responseSuccess(AttractionCategoryResource::collection($categories));
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTree(Request $request)
    {
        $attractionCategories = AttractionCategory::select('id as key', 'laravel_cte.*')
        ->treeOf(function ($q) {
            $q->whereNull('parent_id');
        })
        ->orderBy('id', 'asc');

        return $this->responseSuccess([
            'tree' => $attractionCategories->get()->toTree(),
            'count' => $attractionCategories->count()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAttractionsPageData(Request $request)
    {
        $langId = getLnaguageId($request);
        //Can't apply constraint (limit 3) after eager loading relation, for each relation. (works only for the first relation)
        //Solution: go through each category and use the load()
        $categories = AttractionCategory::whereNull('parent_id')->get();
        $categories->each(function($category) use ($langId) {
            $category->load([
                'attractions' => fn($q) => $q->orderByDesc('id')->limit(3),
                'attractions.thumbnail',
                'attractions.translation' => fn ($query) => $query->where('language_id', $langId),
                'translation' => fn ($query) => $query->where('language_id', $langId),
            ]);
        });
        $trips = Trip::with([
                'thumbnail',
                'translation' => fn ($query) => $query->where('language_id', $langId),
            ])
            ->limit(3)
            ->orderBy('id', 'desc')
            ->get();
        return $this->responseSuccess([
            'categories' => AttractionCategoryResource::collection($categories),
            'trips' => TripResource::collection($trips)
        ]);
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttractionCategoryCreateRequest $request)
    {       
        $user = auth()->user();

        $data = $request->all();
        $data['created_by'] = $user->id;

        $attractionCategory = AttractionCategory::create($data);
        $translationData = $request->all();
        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }
        $translationData['user_id'] = $user->id;
        $translationData['attraction_category_id'] = $attractionCategory->id;
        $attractionCategory->createTranslations($translationData, $language);
        $attractionCategory->load([
            'parent',
            'translation' => fn ($query) => $query->where('language_id', $langId),
        ]);
        return $this->responseSuccess(AttractionCategoryResource::make($attractionCategory));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttractionCategoryUpdateRequest $request, string $id)
    {
        $user = auth()->user();

        $attractionCategory = AttractionCategory::find($id);
        if(!$attractionCategory){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }
        $data['updated_by'] = $user->id;
        //prevent seting parent as parent
        if (!empty($data['parent_id'])) {
            //set parents child as its parent (set child as parent of its parent)
            $parentChildParent = AttractionCategory::where('id', $data['parent_id'])->where('parent_id', $id)->exists();
            if ($data['parent_id'] == $id || $parentChildParent) {
                $data['parent_id'] = null;
            } 
        }
        if ($language->lang_code === Language::SR_CODE) {
            $attractionCategory->update($data); //Update default values
        }

        $translationData = $request->all();
        $translationData['user_id'] = $user->id;
        $translationData['attraction_category_id'] = $attractionCategory->id;
        $attractionCategory->syncTranslations($translationData, $language);

        $attractionCategory->load([
            'parent',
            'translation' => fn ($query) => $query->where('language_id', $langId),
        ]);

        return $this->responseSuccess(AttractionCategoryResource::make($attractionCategory));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $attractionCategory = AttractionCategory::find($id);
        if(!$attractionCategory){
            return $this->responseNotFound();
        }
        $attractionCategory->delete();

        $perPage = $request->perPage ?? 20;
        $attractionCategories = $this->attractionCategoryRepository->getAllFiltered($request->all());
        $attractionCategories->with('parent');
        $attractionCategoriesPaginated = $attractionCategories->paginate($perPage);
        return $this->responseSuccess(AttractionCategoryResourcePaginated::make($attractionCategoriesPaginated));
    }
}
