<?php

namespace App\Http\Controllers;

use App\Contracts\QuestionTypeRepositoryInterface;
use App\Http\Resources\Question\QuestionTypeResource;
use App\Http\Resources\Question\QuestionTypeResourcePaginated;
use App\Models\QuestionType;
use Illuminate\Http\Request;

class QuestionTypeController extends Controller
{
    /**
     * QuestionTypeRepository
     *
     * @var QuestionTypeRepositoryInterface
     */
    private $questionTypeRepository;

    public function __construct(QuestionTypeRepositoryInterface $questionTypeRepository) {
        $this->questionTypeRepository = $questionTypeRepository;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($app, $id)
    {
        $questionType = QuestionType::find($id);
        if(!$questionType){
            return $this->responseNotFound();
        }
        return $this->responseSuccess([
            'question' => QuestionTypeResource::make($questionType)
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
        $questionTypes = $this->questionTypeRepository->getAllFiltered($request->all(), $app);
        //$questions->with('user');
        $questionTypes->with('parent');
        $questionTypesPaginated = $questionTypes->paginate($perPage);
        return $this->responseSuccess([
            'question_types' => QuestionTypeResourcePaginated::make($questionTypesPaginated)
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
        $questionTypes = QuestionType::whereNull('parent_id')
            ->where('app', $app)
            ->orderBy('id')
            ->with('children')
            ->get();
        return $this->responseSuccess([
            'question_root_types' => QuestionTypeResource::collection($questionTypes)
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
            'name' => 'required|string'
		]);
        
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;

        $questionType = QuestionType::create($data);
        $questionType->load('parent');
        return $this->responseSuccess([
            'question_type' => QuestionTypeResource::make($questionType)
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
            'name' => 'required|string'
		]);
       
        $user = auth()->user();

        $questionType = QuestionType::find($id);
        if(!$questionType){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        if(!$questionType->update($data)){
            return $this->responseErrorSavingModel();
        }
        $questionType->load('parent');

        return $this->responseSuccess([
            'question_type' => QuestionTypeResource::make($questionType)
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
        $questionType = QuestionType::find($id);
        if(!$questionType){
            return $this->responseNotFound();
        }
        $questionType->delete();
        return $this->responseSuccess();
    }
}
