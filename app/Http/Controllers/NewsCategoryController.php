<?php

namespace App\Http\Controllers;

use App\Http\Requests\News\NewsCategoryCreateRequest;
use App\Http\Requests\News\NewsCategoryUpdateRequest;
use App\Http\Resources\News\NewsCategoryResource;
use App\Http\Resources\News\NewsCategoryResourcePaginated;
use App\Http\Resources\News\NewsResourcePaginated;
use App\Models\NewsCategory;
use App\Repositories\NewsCategoryRepository;
use App\Repositories\NewsRepository;
use Illuminate\Http\Request;

class NewsCategoryController extends Controller
{
    /**
     * NewsCategoryRepository
     *
     * @var NewsCategoryRepository
     */
    
    private $newsCategoryRepository;

    /**
     * NewsCategoryRepository
     *
     * @var NewsCategoryRepository
     */
    private $newsRepository;

    public function __construct(NewsRepository $newsRepository, NewsCategoryRepository $newsCategoryRepository) {
        $this->newsRepository = $newsRepository;
        $this->newsCategoryRepository = $newsCategoryRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 20;
        $categories = $this->newsCategoryRepository->getAllFiltered($request->all());
        //$regulationTypes->groupBy('name')->select(DB::raw("max(id),max(created_at)"));
        $categories->with('parent');
        $categoriesPaginated = $categories->paginate($perPage);
        return $this->responseSuccess(NewsCategoryResourcePaginated::make($categoriesPaginated));
    }

    /**
     * Display the specified resource.
     */
    public function get(string $id)
    {
        $newsCategory = NewsCategory::find($id);
        if(!$newsCategory){
            return $this->responseNotFound();
        }
        return $this->responseSuccess(NewsCategoryResource::make($newsCategory));
    }

    /**
     * Display a listing of the resource.
     */
    public function getCategoryNews(Request $request, $id)
    {
        $perPage = $request->perPage ?? 20;
        $category = NewsCategory::find($id);

        if (!$category) {
            return $this->responseNotFound();
        }


        $filters = $request->all();
        $news = $this->newsRepository->getAllFiltered($filters);
        $categoriesIds = $category->descendantsAndSelf()->get()->pluck('id')->all();
        $news->whereHas('categories', function($query) use ($categoriesIds) {
            $query->whereIn('news_news_category.news_category_id', $categoriesIds);
        });

        $news->with(['categories', 'images', 'thumbnail']);
        $newsPaginated = $news->paginate($perPage);
        
        $newsResource = NewsResourcePaginated::make($newsPaginated);
        $categoryResource = NewsCategoryResource::make($category);
        return $this->responseSuccess([
            'category' => $categoryResource,
            'news' => $newsResource
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
        $categories = NewsCategory::whereNull('parent_id')->get();
        $categories->each(function($category) {
            $category->load([
                'news' => fn($q) => $q->orderByDesc('id')->limit(3),
                'news.images',
                'news.thumbnail',
            ]);
        });
        return $this->responseSuccess(NewsCategoryResource::collection($categories));
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTree(Request $request)
    {
        $attractionCategories = NewsCategory::select('id as key', 'name as label', 'laravel_cte.*')
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
    public function store(NewsCategoryCreateRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['created_by'] = $user->id;

        $newsCategory = NewsCategory::create($data);
        $newsCategory->load('parent');
        return $this->responseSuccess(NewsCategoryResource::make($newsCategory));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NewsCategoryUpdateRequest $request, string $id)
    {  
        $user = auth()->user();

        $newsCategory = NewsCategory::find($id);
        if(!$newsCategory){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['updated_by'] = $user->id;
        //prevent seting parent as parent
        if (!empty($data['parent_id'])) {
            //set parents child as its parent (set child as parent of its parent)
            $parentChildParent = NewsCategory::where('id', $data['parent_id'])->where('parent_id', $id)->exists();
            if ($data['parent_id'] == $id || $parentChildParent) {
                $data['parent_id'] = null;
            } 
        }
        if(!$newsCategory->update($data)){
            return $this->responseErrorSavingModel();
        }
        $newsCategory->load('parent');
        return $this->responseSuccess(NewsCategoryResource::make($newsCategory));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $newsCategory = NewsCategory::find($id);
        if(!$newsCategory){
            return $this->responseNotFound();
        }
        $newsCategory->delete();

        $perPage = $request->perPage ?? 20;
        $attractionCategories = $this->newsCategoryRepository->getAllFiltered($request->all());
        $attractionCategories->with('parent');
        $attractionCategoriesPaginated = $attractionCategories->paginate($perPage);
        return $this->responseSuccess(NewsCategoryResourcePaginated::make($attractionCategoriesPaginated));
    }
}
