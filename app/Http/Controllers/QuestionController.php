<?php

namespace App\Http\Controllers;

use App\Http\Resources\Question\QuestionResource;
use App\Contracts\QuestionRepositoryInterface;
use App\Http\Resources\Question\QuestionResourcePaginated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Question;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QuestionController extends Controller
{
    /**
     * QuestionRepository
     *
     * @var QuestionRepository
     */
    private $questionRepository;

    public function __construct(QuestionRepositoryInterface $questionRepository) {
        $this->questionRepository = $questionRepository;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($app, $id)
    {
        $question = Question::find($id);
        if(!$question){
            return $this->responseNotFound();
        }
        return $this->responseSuccess([
            'question' => QuestionResource::make($question)
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
        $questions = $this->questionRepository->getAllFiltered($request->all(), $app);
        $questionPaginated = $questions->paginate($perPage);
        $questionResource = QuestionResourcePaginated::make($questionPaginated);
        return $this->responseSuccess([
            'questions' => $questionResource
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
			'question' => 'required|string',
            'answer' => 'required|string'
		]);

        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        $data['publish_date'] = !empty($data['publish_date']) ? Carbon::createFromFormat('d.m.Y.', $data['publish_date']) : date('Y-m-d H:i:s');

        $question = Question::create($data);
        //Upload file
        if(!empty($data['file_path'])){
            $extension = pathinfo(base_path().'/'.$data['file_path'], PATHINFO_EXTENSION);
            $slug = Str::slug((strlen($question->question) > 20) ? substr($question->question, 0, 20) : $question->question);
            $newPath = 'public/questions/'.$question->id.'_'.$slug.'_'.Str::random(6).'.'.$extension;
            Storage::move($data['file_path'], $newPath);
            $question->update(['file_path' => $newPath]);
        }

        return $this->responseSuccess([
            'question' => QuestionResource::make($question)
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
			'question' => 'required|string',
            'answer' => 'required|string'
		]);

        $user = auth()->user();

        $question = Question::find($id);
        if(!$question){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        $data['publish_date'] = !empty($data['publish_date']) ? Carbon::createFromFormat('d.m.Y.', $data['publish_date']) : date('Y-m-d H:i:s');

        //Upload file
        if(!empty($data['file_path']) && $question->file_path !== $data['file_path']){
            $existingFilePath = explode("/", $data['file_path']);
            //Delete the file only from the corresponding folder
            $folder = "question";
            $fileName = $existingFilePath[1];
            if(Storage::exists("$folder/$fileName")){
                Storage::delete("$folder/$fileName");
            }
            $extension = pathinfo(base_path().'/'.$data['file_path'], PATHINFO_EXTENSION);
            $question = (strlen($data['question']) > 20) ? substr($data['question'], 0, 20) : $data['question'];
            $slug = Str::slug($question);
            $newPath = 'public/questions/'.$question->id.'_'.$slug.'_'.Str::random(6).'.'.$extension;
            Storage::move($data['file_path'], $newPath);
            $data['file_path'] = $newPath;
        }

        $question->update($data);
        return $this->responseSuccess([
            'question' => QuestionResource::make($question)
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
        $question = Question::find($id);
        if(!$question){
            return $this->responseNotFound();
        }
        //delete file
        if(!empty($question->file_path)){
            Storage::delete($question->file_path);
        }
        $question->delete();
        return $this->responseSuccess();
    }

    /**
     * Delete question file 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteFile($app, $id)
    {
        $question = Question::find($id);
        if(!$question){
            return $this->responseNotFound();
        }

        $questionPath = $question->file_path;
        if(Storage::exists($questionPath)){
            Storage::delete($questionPath);
            $question->update(['file_path' => NULL]);
            return $this->responseSuccess();
        }
        return $this->responseSuccessMsg('File does not exist');
    }
}
