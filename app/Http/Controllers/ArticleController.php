<?php

namespace App\Http\Controllers;

use App\Http\Resources\Article\ArticleResource;
use App\Contracts\ArticleRepositoryInterface;
use App\Http\Resources\Article\ArticleResourcePaginated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Article;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class ArticleController extends Controller
{
    /**
     * ArticleRepository
     *
     * @var ArticleRepository
     */
    private $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository) {
        $this->articleRepository = $articleRepository;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($app, $id)
    {
        $article = Article::with([
            'articleType.ancestorsAndSelf' => fn ($query) => $query->orderBy('id', 'ASC'),
            'downloadFile', 
            'pdfFile', 
            'htmlFile', 
            'htmlFiles'
        ])->find($id);
        if(!$article){
            return $this->responseNotFound();
        }

        $user = auth()->user();
        if(!$user || (!$user->hasActivePlan($app) && !$user->hasAuthorAccess())){
            return $this->responseSuccess([
                'article' => ArticleResource::make($article)
            ]);
        }

        return $this->responseSuccess([
            'article' => ArticleResource::make($article)
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
        $articles = $this->articleRepository->getAllFiltered($request->all(), $app);
        $articles->with(['pdfFile', 'htmlFile', 'htmlFiles', 'downloadFile']);
        $articlePaginated = $articles->paginate($perPage);
        $articleResource = ArticleResourcePaginated::make($articlePaginated);
        return $this->responseSuccess([
            'articles' => $articleResource
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
            'author' => 'required|string'
		]);

        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        $data['publish_date'] = !empty($data['publish_date']) ? $data['publish_date'] : date('Y-m-d H:i:s');

        $article = Article::create($data);
        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $article->saveFiles($data['tmp_files'], 'articles/');
            $article->convertWordFileToPdf($data['tmp_files'], 'articles/');
        }
        $article->load(['pdfFile', 'htmlFile', 'htmlFiles', 'downloadFile']);

        return $this->responseSuccess([
            'article' => ArticleResource::make($article)
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
			'author' => 'required|string'
		]);

        $user = auth()->user();

        $article = Article::find($id);
        if(!$article){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        $data['publish_date'] = $data['publish_date'] ?? date('Y-m-d H:i:s');

         //Upload files
         if(!empty($data['tmp_files'])){
            $article->saveFiles($data['tmp_files'], 'articles/');
            $article->convertWordFileToPdf($data['tmp_files'], 'articles/');
        }
        $article->update($data);
        $article->load(['pdfFile', 'htmlFile', 'htmlFiles', 'downloadFile']);
        return $this->responseSuccess([
            'article' => ArticleResource::make($article)
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
        $article = Article::find($id);
        if(!$article){
            return $this->responseNotFound();
        }
        //delete file
        $article->deleteAllFiles();
        $article->delete();
        return $this->responseSuccess();
    }

}
