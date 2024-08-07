<?php

namespace App\Http\Controllers;

use App\Http\Requests\News\NewsCreateRequest;
use App\Http\Requests\News\NewsUpdateRequest;
use App\Http\Resources\News\NewsResource;
use App\Http\Resources\News\NewsResourcePaginated;
use App\Models\Language;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\News;
use App\Models\Translations\NewsTranslation;
use App\Repositories\NewsRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NewsController extends Controller
{
    /**
     * NewsRepository
     *
     * @var NewsRepository
     */
    private $newsRepository;

    public function __construct(NewsRepository $newsRepository) {
        $this->newsRepository = $newsRepository;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id, string $langId = null)
    {
        $langId = getSelectedOrDefaultLangId($langId);
        $news = News::with([
                'images',
                'categories',
                'thumbnail',
                'translation' => fn ($query) => $query->where('language_id', $langId)
            ])
            ->find($id);
        if(!$news){
            return $this->responseNotFound();
        }
        $news->translateModelByLangId($langId);

        $categoryIds = $news->categories->pluck('id')->all();
        $newsCategories = [];
        foreach ($news->categories as $category) {
            $categories = $category->ancestorsAndSelf()->get()->toArray();
            $newsCategories[] = $categories;
        }
        $selectedCategories = [];
        foreach ($categoryIds as $cid) {
            $selectedCategories[$cid] = true;
        }

        return $this->responseSuccess([
            'news' => NewsResource::make($news),
            'selected_categories' => $selectedCategories,
            'news_categories' => $newsCategories
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function adminGet($id, string $langId = null)
    {
        $news = News::with([
                'images',
                'categories',
                'thumbnail',
                'translations',
            ])
            ->find($id);
        if(!$news){
            return $this->responseNotFound();
        }

        if ($langId) {
            if (!$news->translateModelByLangId($langId)) {
                $news->emptyModel(new NewsTranslation());
            } 
        } else {
            $news->translateModelByLangCode(Language::SR_CODE);
        }

        $categoryIds = $news->categories->pluck('id')->all();
        $newsCategories = [];
        foreach ($news->categories as $category) {
            $categories = $category->ancestorsAndSelf()->get()->toArray();
            $newsCategories[] = $categories;
        }
        $selectedCategories = [];
        foreach ($categoryIds as $cid) {
            $selectedCategories[$cid] = true;
        }

        return $this->responseSuccess([
            'news' => NewsResource::make($news),
            'selected_categories' => $selectedCategories,
            'news_categories' => $newsCategories
        ]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 20;
        $news = $this->newsRepository->getAllFiltered($request->all());
        $news->with(['images', 'thumbnail']);
        $newsPaginated = $news->paginate($perPage);
        $newsResource = NewsResourcePaginated::make($newsPaginated);
        return $this->responseSuccess($newsResource);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewsCreateRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['created_by'] = $user->id;
        $data['slug'] = Str::slug(substr($data['title'], 0, 128));
        $data['publish_date'] = !empty($data['publish_date']) ? Carbon::createFromFormat('d.m.Y.', $data['publish_date']) : date('Y-m-d H:i:s');

        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }

        $news = News::create($data);
        if (!empty($data['category_ids'])) {
            $news->categories()->attach($data['category_ids']);
        }
        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $news->saveFiles($data['tmp_files'], 'news/');
        }
        if (!empty($news->images) && !empty($news->images[0])) {
            $news->images[0]->makeThumbnail();
        }

        $translationData = $request->all();
        $translationData['user_id'] = $user->id;
        $translationData['news_id'] = $news->id;
        $news->createTranslations($translationData, $language);
        $news->load([
            'images',
            'thumbnail',
            'categories',
            'translations'
        ]);

        return $this->responseSuccess(NewsResource::make($news));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NewsUpdateRequest $request, $id)
    {
        $user = auth()->user();

        $news = News::find($id);
        if(!$news){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['updated_by'] = $user->id;
        $data['publish_date'] = !empty($data['publish_date']) ? Carbon::createFromFormat('d.m.Y.', $data['publish_date']) : date('Y-m-d H:i:s');
        $data['slug'] = Str::slug(substr($data['title'], 0, 128));

        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }

        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $news->saveFiles($data['tmp_files'], 'news/');
        }      

        if ($language->lang_code === Language::SR_CODE) {
            $news->update($data); //Update default values
        }
        $translationData = $request->all();
        $translationData['user_id'] = $user->id;
        $translationData['news_id'] = $news->id;
        $news->syncTranslations($translationData, $language);

        if (!empty($data['category_ids'])) {
            $news->categories()->sync($data['category_ids']);
        }

        $news->load(['images', 'thumbnail', 'categories', 'translations']);

        return $this->responseSuccess(NewsResource::make($news));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $news = News::find($id);
        if(!$news){
            return $this->responseNotFound();
        }
        //delete file
        $news->deleteAllFiles();
        $news->delete();
        return $this->responseSuccess();
    }

}
