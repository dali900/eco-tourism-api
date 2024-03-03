<?php

namespace App\Http\Controllers;

use App\Contracts\ArticleTypeRepositoryInterface;
use App\Http\Resources\Article\ArticleTypeResource;
use App\Http\Resources\Article\ArticleTypeResourcePaginated;
use App\Models\ArticleType;
use Illuminate\Http\Request;

class ArticleTypeController extends Controller
{
    /**
     * ArticleTypeRepository
     *
     * @var ArticleTypeRepositoryInterface
     */
    private $articleTypeRepository;

    public function __construct(ArticleTypeRepositoryInterface $articleTypeRepository) {
        $this->articleTypeRepository = $articleTypeRepository;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($app, $id)
    {
        $articleType = ArticleType::find($id);
        if(!$articleType){
            return $this->responseNotFound();
        }
        return $this->responseSuccess([
            'article' => ArticleTypeResource::make($articleType)
        ]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request,$app)
    {
        $perPage = $request->perPage ?? 20;
        $articleTypes = $this->articleTypeRepository->getAllFiltered($request->all(), $app);
        //$articles->with('user');
        $articleTypes->with('parent.children');
        $articleTypesPaginated = $articleTypes->paginate($perPage);
        return $this->responseSuccess([
            'article_types' => ArticleTypeResourcePaginated::make($articleTypesPaginated)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getRoots(Request $request, $app)
    {
        return $this->responseSuccess([
            'article_root_types' => ArticleType::whereApp($app)->whereNull('parent_id')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$app)
    {
        $attr = $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|numeric'
		]);
        
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;

        $articleType = ArticleType::create($data);
        $articleType->load('parent');
        return $this->responseSuccess([
            'article_type' => ArticleTypeResource::make($articleType)
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
            'name' => 'required|string',
            'parent_id' => 'nullable|numeric'
		]);
       
        $user = auth()->user();

        $articleType = ArticleType::find($id);
        if(!$articleType){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        if(!$articleType->update($data)){
            return $this->responseErrorSavingModel();
        }
        $articleType->load('parent');

        return $this->responseSuccess([
            'article_type' => ArticleTypeResource::make($articleType)
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
        $articleType = ArticleType::find($id);
        if(!$articleType){
            return $this->responseNotFound();
        }
        $articleType->delete();
        return $this->responseSuccess();
    }
}
