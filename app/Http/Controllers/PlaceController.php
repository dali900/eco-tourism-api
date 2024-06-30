<?php

namespace App\Http\Controllers;

use App\Http\Requests\Place\PlaceCreateRequest;
use App\Http\Requests\Place\PlaceUpdateRequest;
use App\Http\Resources\PlaceResource;
use App\Http\Resources\PlaceResourcePaginated;
use App\Models\Language;
use App\Models\Place;
use App\Models\Translations\PlaceTranslation;
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
        $langId = getLnaguageId($request);

        $perPage = $request->perPage ?? 20;
        $place = Place::with([
            'translation' => fn ($query) => $query->where('language_id', $langId)
        ]);
        $places = $this->placeRepository->getAllFiltered($request->all(), $place);

        $places->with(['thumbnail']);
        $placesPaginated = $places->paginate($perPage);
        
        $placeResource = PlaceResourcePaginated::make($placesPaginated);
        return $this->responseSuccess($placeResource);
    }

    /**
     * Display the specified resource.
     */
    public function get(string $id, string $langId = null)
    {
        $place = Place::with([
            'images',
            'thumbnail',
            'attractions.thumbnail', 
            'attractions.translation' => fn ($query) => $query->where('language_id', $langId),
            'translation' => fn ($query) => $query->where('language_id', $langId),
        ])
        ->find($id);
        if(!$place){
            return $this->responseNotFound();
        }

        if ($langId) {
            if ($place->relationLoaded('translations') && !empty($place->translations[0])) {
                $place->translateFromModel($place->translations[0]);
            } else {
                $place->translateModelByLangCode(Language::SR_CODE);
            }
        }
        
        return $this->responseSuccess(PlaceResource::make($place));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function adminGet($id, string $langId = null)
    {
        $place = Place::with([
            'images', 
            'thumbnail', 
            'attractions.thumbnail',
            'translations'
        ])->find($id);
        if(!$place){
            return $this->responseNotFound();
        }

        if ($langId) {
            if (!$place->translateModelByLangId($langId)) {
                $place->emptyModel(new PlaceTranslation());
            } 
        } else {
            $place->translateModelByLangCode(Language::SR_CODE);
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
        $data['created_by'] = $user->id;
        $place = Place::create($data);
        
        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $place->saveFiles($data['tmp_files'], 'places/');
        }
        if (!empty($place->images) && !empty($place->images[0])) {
            $place->images[0]->makeThumbnail();
        }
        //translation
        $translationData = $request->all();
        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }
        $translationData['user_id'] = $user->id;
        $translationData['place_id'] = $place->id;
        $place->createTranslations($translationData, $language);

        $place->load([
            'images',
            'thumbnail',
            'translation' => fn ($query) => $query->where('language_id', $langId),
        ]);

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
        $data['updated_by'] = $user->id;
        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }

        //Upload files
        if(!empty($data['tmp_files'])){
            $place->saveFiles($data['tmp_files'], 'places/');
        }

        if ($language->lang_code === Language::SR_CODE) {
            $place->update($data);//Update default values
        }

        $translationData = $request->all();
        $translationData['user_id'] = $user->id;
        $translationData['place_id'] = $place->id;
        $place->syncTranslations($translationData, $language);
        $place->load([
            'images',
            'thumbnail',
            'translation' => fn ($query) => $query->where('language_id', $langId),
        ]);
        
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
