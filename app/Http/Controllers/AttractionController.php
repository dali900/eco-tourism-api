<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attraction\AttractionCreateRequest;
use App\Http\Requests\Attraction\AttractionUpdateRequest;
use App\Http\Resources\Attraction\AttractionResource;
use App\Http\Resources\Attraction\AttractionResourcePaginated;
use App\Models\Attraction;
use App\Repositories\AttractionRepository;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class AttractionController extends Controller
{
    /**
     * AttractionRepository
     *
     * @var AttractionRepository
     */
    private $attractionRepository;

    public function __construct(AttractionRepository $attractionRepository) {
        $this->attractionRepository = $attractionRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 20;
        $attractions = $this->attractionRepository->getAllFiltered($request->all());

        $attractions->with(['category', 'defaultImage']);
        $attractionsPaginated = $attractions->paginate($perPage);
        
        $attractionResource = AttractionResourcePaginated::make($attractionsPaginated);
        return $this->responseSuccess($attractionResource);
    }
    
    /**
     * Display the specified resource.
     */
    public function get(string $id)
    {
        //$rt = \App\Models\RegulationType::treeOf(function($q){$q->where('id',83);})->get()->toTree();
        $attraction = Attraction::with([
            'category.ancestorsAndSelf' => fn ($query) => $query->orderBy('id', 'ASC'),
            'images'
        ])->find($id);
        if(!$attraction){
            return $this->responseNotFound();
        }
        
        return $this->responseSuccess(AttractionResource::make($attraction));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttractionCreateRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['created_by'] = $user->id;
        $attraction = Attraction::create($data);
        
        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $attraction->saveFiles($data['tmp_files'], 'attractions/');
        }
        $attraction->load(['images']);

        return $this->responseSuccess(AttractionResource::make($attraction));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttractionUpdateRequest $request, string $id)
    {
        $user = auth()->user();
        
        $attraction = Attraction::find($id);
        if(!$attraction){
            return $this->responseNotFound();
        }
        
        if ($user->id != $attraction->user_id && !$user->hasEditorAccess()){
            return $this->responseForbidden();
        }
        

        $data = $request->all();
        $data['updated_by'] = $user->id;
        //Upload files
        if(!empty($data['tmp_files'])){
            $attraction->saveFiles($data['tmp_files'], 'attractions/');
        }
        $attraction->update($data);
        $attraction->load(['images']);
        
        return $this->responseSuccess(AttractionResource::make($attraction));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = auth()->user();
        $attraction = Attraction::find($id);
        if(!$attraction){
            return $this->responseNotFound();
        }
        if ($user->id != $attraction->user_id && !$user->hasEditorAccess()){
            return $this->responseForbidden();
        }
        //delete file
        $attraction->deleteAllFiles();
        $attraction->delete();
        return $this->responseSuccess();
    }
}
