<?php

namespace App\Http\Controllers;

use App\Http\Resources\Attraction\AttractionCategoryResource;
use App\Http\Resources\Attraction\AttractionCategoryResourcePaginated;
use App\Http\Resources\Attraction\AttractionResource;
use App\Http\Resources\Attraction\AttractionResourcePaginated;
use App\Models\AttractionCategory;
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
    public function get(string $id)
    {
        $attractionCategory = AttractionCategory::find($id);
        if(!$attractionCategory){
            return $this->responseNotFound();
        }
        return $this->responseSuccess([
            'regulation' => AttractionCategoryResource::make($attractionCategory)
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function getCatagoryAttractions(Request $request, $id)
    {
        $perPage = $request->perPage ?? 20;
        $category = AttractionCategory::find($id);

        if (!$category) {
            return $this->responseNotFound();
        }

        $categoriesIds = $category->descendantsAndSelf()->get()->pluck('id')->all();
        $filters = $request->all();
        if ($categoriesIds) {
            $filters['category_ids'] = $categoriesIds;
        }
        $attractions = $this->attractionRepository->getAllFiltered($filters);

        $attractions->with(['category', 'defaultImage']);
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attr = $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|numeric'
		]);
        
        $user = auth()->user();

        $data = $request->all();
        $data['created_by'] = $user->id;

        $attractionCategory = AttractionCategory::create($data);
        $attractionCategory->load('parent');
        return $this->responseSuccess(AttractionCategoryResource::make($attractionCategory));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $attr = $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|numeric'
		]);
       
        $user = auth()->user();

        $attractionCategory = AttractionCategory::find($id);
        if(!$attractionCategory){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['updated_by'] = $user->id;
        //prevent seting parent as parent
        if (!empty($data['parent_id'])) {
            //set parents child as its parent (set child as parent of its parent)
            $parentChildParent = AttractionCategory::where('id', $data['parent_id'])->where('parent_id', $id)->exists();
            if ($data['parent_id'] == $id || $parentChildParent) {
                $data['parent_id'] = null;
            } 
        }
        if(!$attractionCategory->update($data)){
            return $this->responseErrorSavingModel();
        }
        $attractionCategory->load('parent');
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
