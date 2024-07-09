<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ad\AdCategoryCreateRequest;
use App\Http\Requests\Ad\AdCategoryUpdateRequest;
use App\Http\Resources\Ad\AdCategoryResource;
use App\Http\Resources\Ad\AdCategoryResourcePaginated;
use App\Models\AdCategory;
use App\Models\Language;
use App\Models\Translations\AdCategoryTranslation;
use App\Repositories\AdCategoryRepository;
use App\Repositories\AdRepository;
use Illuminate\Http\Request;

class AdCategoryController extends Controller
{
    /**
     * AdCategoryRepository
     *
     * @var AdCategoryRepository
     */
    
     private $adCategoryRepository;

     /**
      * AdRepository
      *
      * @var AdRepository
      */
     private $adRepository;
 
     public function __construct(AdCategoryRepository $adCategoryRepository, AdRepository $adRepository) {
         $this->adCategoryRepository = $adCategoryRepository;
         $this->adRepository = $adRepository;
     }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 20;
        $categories = $this->adCategoryRepository->getAllFiltered($request->all());
        //$regulationTypes->groupBy('name')->select(DB::raw("max(id),max(created_at)"));
        $categories->with('parent');
        $categoriesPaginated = $categories->paginate($perPage);
        return $this->responseSuccess(AdCategoryResourcePaginated::make($categoriesPaginated));
    }

    /**
     * Display the specified resource.
     */
    public function get(string $id, string $langId = null)
    {
        $langId = getSelectedOrDefaultLangId($langId);
        $adCategory = AdCategory::with([
                'translation' => fn ($query) => $query->where('language_id', $langId)
            ])
            ->find($id);
        if(!$adCategory){
            return $this->responseNotFound();
        }
        $adCategory->translateModelByLangId($langId);

        return $this->responseSuccess(AdCategoryResource::make($adCategory));
    }

    /**
     * Display the specified resource.
     */
    public function adminGet(string $id, string $langId = null)
    {
        $langId = getSelectedOrDefaultLangId($langId);
        $adCategory = AdCategory::with([
                'translations',
                'translation' => fn ($query) => $query->where('language_id', $langId)
            ])
            ->find($id);
        if(!$adCategory){
            return $this->responseNotFound();
        }

        if ($langId) {
            if (!$adCategory->translateModelByLangId($langId)) {
                $adCategory->emptyModel(new AdCategoryTranslation());
            } 
        } else {
            $adCategory->translateModelByLangCode(Language::SR_CODE);
        }

        return $this->responseSuccess(AdCategoryResource::make($adCategory));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTree(Request $request)
    {
        $adCategories = AdCategory::select('id as key', 'name as label', 'laravel_cte.*')
        ->treeOf(function ($q) {
            $q->whereNull('parent_id');
        })
        ->orderBy('id', 'asc');

        return $this->responseSuccess([
            'tree' => $adCategories->get()->toTree(),
            'count' => $adCategories->count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdCategoryCreateRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['created_by'] = $user->id;

        $adCategory = AdCategory::create($data);
        $translationData = $request->all();
        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }
        $translationData['user_id'] = $user->id;
        $translationData['ad_category_id'] = $adCategory->id;
        $adCategory->createTranslations($translationData, $language);
        $adCategory->load([
            'parent',
            'translation' => fn ($query) => $query->where('language_id', $langId),
        ]);
        return $this->responseSuccess(AdCategoryResource::make($adCategory));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdCategoryUpdateRequest $request, string $id)
    {
        $user = auth()->user();

        $adCategory = AdCategory::find($id);
        if(!$adCategory){
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
            $parentChildParent = AdCategory::where('id', $data['parent_id'])->where('parent_id', $id)->exists();
            if ($data['parent_id'] == $id || $parentChildParent) {
                $data['parent_id'] = null;
            } 
        }
        if ($language->lang_code === Language::SR_CODE) {
            $adCategory->update($data); //Update default values
        }

        $translationData = $request->all();
        $translationData['user_id'] = $user->id;
        $translationData['ad_category_id'] = $adCategory->id;
        $adCategory->syncTranslations($translationData, $language);

        $adCategory->load([
            'parent',
            'translation' => fn ($query) => $query->where('language_id', $langId),
        ]);

        return $this->responseSuccess(AdCategoryResource::make($adCategory));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $adCategory = AdCategory::find($id);
        if(!$adCategory){
            return $this->responseNotFound();
        }
        $adCategory->delete();

        $perPage = $request->perPage ?? 20;
        $adCategories = $this->adCategoryRepository->getAllFiltered($request->all());
        $adCategories->with('parent');
        $adCategoriesPaginated = $adCategories->paginate($perPage);
        return $this->responseSuccess(AdCategoryResourcePaginated::make($adCategoriesPaginated));
    }
}
