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
use App\Models\Language;
use App\Models\Translations\TripTranslation;
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
        $langId = getLnaguageId($request);

        $perPage = $request->perPage ?? 20;
        $trips = Trip::with([
            'thumbnail',
            'translation' => fn ($query) => $query->where('language_id', $langId),
        ]);
        $trips = $this->tripRepository->getAllFiltered($request->all(), $trips);
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
    public function get($id, string $langId = null)
    {
        $trip = Trip::with([
            'images', 
            'thumbnail', 
            'attractions.thumbnail',
            'attractions.translation' => fn ($query) => $query->where('language_id', $langId),
            'translation' => fn ($query) => $query->where('language_id', $langId),
        ])->find($id);
        if(!$trip){
            return $this->responseNotFound();
        }

        if ($langId) {
            if ($trip->relationLoaded('translations') && !empty($trip->translations[0])) {
                $trip->translateFromModel($trip->translations[0]);
            } else {
                $trip->translateModelByLangCode(Language::SR_CODE);
            }
        }

        return $this->responseSuccess(TripResource::make($trip));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function adminGet($id, string $langId = null)
    {
        $trip = Trip::with([
            'images', 
            'thumbnail', 
            'attractions.thumbnail',
            'translations'
        ])->find($id);
        if(!$trip){
            return $this->responseNotFound();
        }

        if ($langId) {
            if (!$trip->translateModelByLangId($langId)) {
                $trip->emptyModel(new TripTranslation());
            } 
        } else {
            $trip->translateModelByLangCode(Language::SR_CODE);
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

        $translationData = $request->all();
        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }
        $translationData['user_id'] = $user->id;
        $translationData['trip_id'] = $trip->id;
        $trip->createTranslations($translationData, $language);
        $trip->load([
            'images',
            'thumbnail',
            'attractions',
            'translation' => fn ($query) => $query->where('language_id', $langId),
        ]);

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
        
        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }

        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $trip->saveFiles($data['tmp_files'], 'trip/');
        } 

        if ($language->lang_code === Language::SR_CODE) {
            $trip->update($data);//Update default values
        }
        
        if (!empty($data['attraction_ids'])) {
            $pivotData = [];
            foreach ($data['attraction_ids'] as $id) {
                $pivotData[$id] = ['updated_at' => Carbon::now()];
            }
            $trip->attractions()->sync($pivotData);
        }

        $translationData = $request->all();
        $translationData['user_id'] = $user->id;
        $translationData['trip_id'] = $trip->id;
        $trip->syncTranslations($translationData, $language);
        $trip->load([
            'images',
            'thumbnail',
            'attractions',
            'translation' => fn ($query) => $query->where('language_id', $langId),
        ]);

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
