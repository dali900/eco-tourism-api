<?php

namespace App\Http\Controllers;

use App\Http\Requests\Place\PlaceCreateRequest;
use App\Http\Requests\Place\PlaceUpdateRequest;
use App\Http\Resources\PlaceResource;
use App\Http\Resources\PlaceResourcePaginated;
use App\Models\Place;
use App\Repositories\PlaceRepository;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    /**
     * PlaceRepository
     *
     * @var PlaceRepository
     */
    private $placeRepository;

    public function __construct(PlaceRepository $placeRepository) {
        $this->placeRepository = $placeRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 20;
        $places = $this->placeRepository->getAllFiltered($request->all());

        $places->with(['defaultImage']);
        $placesPaginated = $places->paginate($perPage);
        
        $placeResource = PlaceResourcePaginated::make($placesPaginated);
        return $this->responseSuccess($placeResource);
    }

    /**
     * Display the specified resource.
     */
    public function get(string $id)
    {
        $place = Place::with([
            'images'
        ])->find($id);
        if(!$place){
            return $this->responseNotFound();
        }
        
        return $this->responseSuccess(PlaceResource::make($place));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlaceCreateRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;
        $place = Place::create($data);
        
        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $place->saveFiles($data['tmp_files'], 'places/');
        }
        $place->load(['images']);

        return $this->responseSuccess(PlaceResource::make($place));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlaceUpdateRequest $request, string $id)
    {
        $user = auth()->user();
        
        $place = Place::find($id);
        if(!$place){
            return $this->responseNotFound();
        }
        
        if ($user->id != $place->user_id && !$user->hasEditorAccess()){
            return $this->responseForbidden();
        }
        

        $data = $request->all();
        $data['user_id'] = $user->id;
        //Upload files
        if(!empty($data['tmp_files'])){
            $place->saveFiles($data['tmp_files'], 'places/');
        }
        $place->update($data);
        $place->load(['images']);
        
        return $this->responseSuccess(PlaceResource::make($place));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        $place = Place::find($id);
        if(!$place){
            return $this->responseNotFound();
        }
        if ($user->id != $place->user_id && !$user->hasEditorAccess()){
            return $this->responseForbidden();
        }
        //delete file
        $place->deleteAllFiles();
        $place->delete();
        return $this->responseSuccess();
    }
}
