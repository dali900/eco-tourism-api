<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Repositories\TripRepository;
use App\Http\Resources\Trip\TripResource;
use App\Http\Requests\Trip\TripCreateRequest;
use App\Http\Requests\Trip\TripUpdateRequest;
use App\Http\Resources\Trip\TripResourcePaginated;
use Carbon\Carbon;

class TripController extends Controller
{
    /**
     * TripRepository
     *
     * @var TripRepository
     */
    private $tripRepository;

    public function __construct(TripRepository $tripRepository) {
        $this->tripRepository = $tripRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 20;
        $trips = $this->tripRepository->getAllFiltered($request->all());
        $trips->with(['thumbnail']);
        $newsPaginated = $trips->paginate($perPage);
        $tripResource = TripResourcePaginated::make($newsPaginated);
        return $this->responseSuccess($tripResource);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $trip = Trip::with(['images', 'thumbnail', 'attractions.thumbnail'])->find($id);
        if(!$trip){
            return $this->responseNotFound();
        }

        return $this->responseSuccess(TripResource::make($trip));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TripCreateRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['created_by'] = $user->id;
        $data['slug'] = Str::slug(substr($data['title'], 0, 128));
        $data['publish_date'] = !empty($data['publish_date']) ? Carbon::createFromFormat('d.m.Y.', $data['publish_date']) : date('Y-m-d H:i:s');

        $trip = Trip::create($data);
        if (!empty($data['attraction_ids'])) {
            $trip->attractions()->attach($data['attraction_ids'], ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        }
        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $trip->saveFiles($data['tmp_files'], 'trip/');
        }
        if (!empty($trip->images) && !empty($trip->images[0])) {
            $trip->images[0]->makeThumbnail();
        }
        $trip->load(['images', 'thumbnail', 'attractions']);

        return $this->responseSuccess(TripResource::make($trip));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TripUpdateRequest $request, string $id)
    {
        $user = auth()->user();

        $trip = Trip::find($id);
        if(!$trip){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['updated_by'] = $user->id;
        $data['publish_date'] = !empty($data['publish_date']) ? Carbon::createFromFormat('d.m.Y.', $data['publish_date']) : date('Y-m-d H:i:s');
        $data['slug'] = Str::slug(substr($data['title'], 0, 128));

        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $trip->saveFiles($data['tmp_files'], 'trip/');
        }      
        $trip->update($data);
        if (!empty($data['attraction_ids'])) {
            $pivotData = [];
            foreach ($data['attraction_ids'] as $id) {
                $pivotData[$id] = ['updated_at' => Carbon::now()];
            }
            $trip->attractions()->sync($pivotData);
        }
        $trip->load(['images', 'thumbnail', 'attractions']);

        return $this->responseSuccess(TripResource::make($trip));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $trip = Trip::find($id);
        if(!$trip){
            return $this->responseNotFound();
        }
        //delete file
        $trip->deleteAllFiles();
        $trip->delete();
        return $this->responseSuccess();
    }
}
