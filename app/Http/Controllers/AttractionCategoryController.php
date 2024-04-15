<?php

namespace App\Http\Controllers;

use App\Http\Resources\Attraction\AttractionCategoryResource;
use App\Http\Resources\Attraction\AttractionCategoryResourcePaginated;
use App\Models\AttractionCategory;
use App\Repositories\AttractionCategoryRepository;
use Illuminate\Http\Request;

class AttractionCategoryController extends Controller
{
    /**
     * AttractionCategoryRepository
     *
     * @var AttractionCategoryRepository
     */
    private $attractionCategoryRepository;

    public function __construct(AttractionCategoryRepository $attractionCategoryRepository) {
        $this->attractionCategoryRepository = $attractionCategoryRepository;
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getRoots(Request $request)
    {
        return $this->responseSuccess(AttractionCategory::whereNull('parent_id')->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTree(Request $request, $app)
    {
        $attractionCategories = AttractionCategory::select('id as key', 'laravel_cte.*')
        ->treeOf(function ($q) use ($app) {
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
        $data['user_id'] = $user->id;

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
        $data['user_id'] = $user->id;
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
        return $this->responseSuccess(AttractionCategoryResource::make($attractionCategoriesPaginated));
    }
}
