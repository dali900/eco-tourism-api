<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ad\AdCreateRequest;
use App\Http\Requests\Ad\AdUpdateRequest;
use App\Http\Resources\Ad\AdResource;
use App\Http\Resources\Ad\AdResourcePaginated;
use App\Models\Ad;
use App\Models\Language;
use App\Models\Place;
use App\Models\Translations\AdTranslation;
use App\Repositories\AdRepository;
use Illuminate\Http\Request;

class AdController extends Controller
{

    /**
     * AdRepository
     *
     * @var AdRepository
     */
    private $adRepository;

    public function __construct(AdRepository $adRepository) {
        $this->adRepository = $adRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $langId = getLnaguageId($request);

        $perPage = $request->perPage ?? 20;
        $ads = Ad::with([
            'thumbnail',
            'translation' => fn ($query) => $query->where('language_id', $langId),
            'place.translation' => fn ($query) => $query->where('language_id', $langId),
            'category.translation' => fn ($query) => $query->where('language_id', $langId),
        ]);
        $ads = $this->adRepository->getAllFiltered($request->all(), $ads);
        $adsPaginated = $ads->paginate($perPage);
        
        $adResource = AdResourcePaginated::make($adsPaginated);
        return $this->responseSuccess($adResource);
    }

    /**
     * Display the specified resource.
     */
    public function get(string $id, string $langId = null)
    {
        $langId = getSelectedOrDefaultLangId($langId);
        $ad = Ad::with([
            'category.ancestorsAndSelf' => fn ($query) => $query->orderBy('id', 'ASC'),
            'category.translation' => fn ($query) => $query->where('language_id', $langId),
            'translation' => fn ($query) => $query->where('language_id', $langId),
            'images',
            'thumbnail',
            'place',
            'place.translation' => fn ($query) => $query->where('language_id', $langId),
        ])->find($id);
        if(!$ad){
            return $this->responseNotFound();
        }

        return $this->responseSuccess(AdResource::make($ad));
    }

    public function adminGet(string $id, string $langId = null)
    {
        $ad = Ad::with([
            'category.ancestorsAndSelf' => fn ($query) => $query->orderBy('id', 'ASC'),
            'translations',
            'images',
            'thumbnail',
            'place',
        ])->find($id);
        if(!$ad){
            return $this->responseNotFound();
        }

        if ($langId) {
            if (!$ad->translateModelByLangId($langId)) {
                $ad->emptyModel(new AdTranslation());
            } 
        } else {
            $ad->translateModelByLangCode(Language::SR_CODE);
        }

        return $this->responseSuccess([
            'ad' => AdResource::make($ad),
            'languages' => Language::get(),
            'places' => Place::get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdCreateRequest $request)
    {
        $user = auth()->user();
        
        $data = $request->all();
        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }
        $data['created_by'] = $user->id;
        $ad = Ad::create($data);

        $translationData = $request->all();
        $translationData['user_id'] = $user->id;
        $translationData['ad_id'] = $ad->id;
        $ad->createTranslations($translationData, $language);
        
        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $ad->saveFiles($data['tmp_files'], 'ads/');
        }
        $ad->load(['images', 'thumbnail']);
        if (!empty($ad->images) && !empty($ad->images[0])) {
            $ad->images[0]->makeThumbnail();
        }
        

        $ad->load(['translations']);

        return $this->responseSuccess(AdResource::make($ad));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdUpdateRequest $request, string $id)
    {
        $user = auth()->user();
        
        $ad = Ad::find($id);
        if(!$ad){
            return $this->responseNotFound();
        }
        
        $data = $request->all();
        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }
        
        if ($user->id != $ad->user_id && !$user->hasEditorAccess()){
            return $this->responseForbidden();
        }

        $data['updated_by'] = $user->id;
        //Upload files
        if(!empty($data['tmp_files'])){
            $ad->saveFiles($data['tmp_files'], 'ads/');
        }
        if ($language->lang_code === Language::SR_CODE) {
            $ad->update($data); //Update default values
        }
        $translationData = $request->all();
        $translationData['user_id'] = $user->id;
        $translationData['ad_id'] = $ad->id;
        $ad->syncTranslations($translationData, $language);

        $ad->load(['images', 'thumbnail', 'translations']);
        
        return $this->responseSuccess(AdResource::make($ad));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        $ad = Ad::find($id);
        if(!$ad){
            return $this->responseNotFound();
        }
        /* if ($user->id != $ad->created_by && !$user->hasEditorAccess()){
            return $this->responseForbidden();
        } */
        //delete file
        $ad->deleteAllFiles();
        $ad->delete();
        return $this->responseSuccess();
    }
}
