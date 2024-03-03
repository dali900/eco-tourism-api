<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Contracts\NewsRepositoryInterface;
use App\Http\Resources\NewsResourcePaginated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\News;
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

    public function __construct(NewsRepositoryInterface $newsRepository) {
        $this->newsRepository = $newsRepository;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($app, $id)
    {
        $news = News::with('image')->find($id);
        if(!$news){
            return $this->responseNotFound();
        }
        return $this->responseSuccess([
            'news' => NewsResource::make($news)
        ]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request, $app)
    {
        $perPage = $request->perPage ?? 20;
        $news = $this->newsRepository->getAllFiltered($request->all(), $app);
        $news->with('image');
        $newsPaginated = $news->paginate($perPage);
        $newsResource = NewsResourcePaginated::make($newsPaginated);
        return $this->responseSuccess([
            'news' => $newsResource
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $app)
    {
        $attr = $request->validate([
			'title' => 'required|string',
            'summary' => 'required|string',
            'subtitle' => 'required|string',
            'text' => 'required|string'
		]);

        $user = auth()->user();

        $data = $request->all();
        $data['app'] = $app;
        $data['user_id'] = $user->id;
        $data['publish_date'] = !empty($data['publish_date']) ? Carbon::createFromFormat('d.m.Y.', $data['publish_date']) : date('Y-m-d H:i:s');

        $news = News::create($data);
        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $news->saveFiles($data['tmp_files'], 'news/');
        }
        $news->load('image');

        return $this->responseSuccess([
            'news' => NewsResource::make($news)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $app, $id)
    {
        $attr = $request->validate([
			'title' => 'required|string',
			'summary' => 'required|string'
		]);

        $user = auth()->user();

        $news = News::find($id);
        if(!$news){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['app'] = $app;
        $data['user_id'] = $user->id;
        $data['publish_date'] = !empty($data['publish_date']) ? Carbon::createFromFormat('d.m.Y.', $data['publish_date']) : date('Y-m-d H:i:s');

        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $news->saveFiles($data['tmp_files'], 'news/');
        }
        
        $news->update($data);
        $news->load('image');
        return $this->responseSuccess([
            'news' => NewsResource::make($news)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($app, $id)
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

    /**
     * Delete news file 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteFile($app, $id)
    {
        $news = News::find($id);
        if(!$news){
            return $this->responseNotFound();
        }

        $newsPath = $news->file_path;
        if(Storage::exists($newsPath)){
            Storage::delete($newsPath);
            $news->update(['file_path' => NULL]);
            return $this->responseSuccess();
        }
        return $this->responseSuccessMsg('File does not exist');
    }
}
