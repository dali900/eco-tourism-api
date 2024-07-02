<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attraction\AttractionCreateRequest;
use App\Http\Requests\Attraction\AttractionUpdateRequest;
use App\Http\Requests\Attraction\CreateTranslationRequest;
use App\Http\Requests\Attraction\UpdateTranslationRequest;
use App\Http\Resources\Attraction\AttractionResource;
use App\Http\Resources\Attraction\AttractionResourcePaginated;
use App\Models\Attraction;
use App\Models\Language;
use App\Models\Place;
use App\Models\Translations\AttractionTranslation;
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
        $langId = getLnaguageId($request);

        $perPage = $request->perPage ?? 20;
        $attractions = Attraction::with([
            'category',
            'thumbnail',
            'translations' => fn ($query) => $query->where('language_id', $langId),
        ]);
        $attractions = $this->attractionRepository->getAllFiltered($request->all(), $attractions);
        $attractionsPaginated = $attractions->paginate($perPage);
        
        $attractionResource = AttractionResourcePaginated::make($attractionsPaginated);
        return $this->responseSuccess($attractionResource);
        
    }
    
    /**
     * Display the specified resource.
     */
    public function get(string $id, string $langId = null)
    {
        $langId = getSelectedOrDefaultLangId($langId);
        $attraction = Attraction::with([
            'category.ancestorsAndSelf' => fn ($query) => $query->orderBy('id', 'ASC'),
            'category.translation' => fn ($query) => $query->where('language_id', $langId),
            'translation' => fn ($query) => $query->where('language_id', $langId),
            'images',
            'thumbnail',
            'place',
            'place.translation' => fn ($query) => $query->where('language_id', $langId),
        ])->find($id);
        if(!$attraction){
            return $this->responseNotFound();
        }

        return $this->responseSuccess(AttractionResource::make($attraction));
    }

    public function adminGet(string $id, string $langId = null)
    {
        $attraction = Attraction::with([
            'category.ancestorsAndSelf' => fn ($query) => $query->orderBy('id', 'ASC'),
            'translations',
            'images',
            'thumbnail',
            'place',
        ])->find($id);
        if(!$attraction){
            return $this->responseNotFound();
        }

        if ($langId) {
            if (!$attraction->translateModelByLangId($langId)) {
                $attraction->emptyModel(new AttractionTranslation());
            } 
        } else {
            $attraction->translateModelByLangCode(Language::SR_CODE);
        }

        return $this->responseSuccess([
            'attraction' => AttractionResource::make($attraction),
            'languages' => Language::get(),
            'places' => Place::get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttractionCreateRequest $request)
    {
        $user = auth()->user();
        
        $data = $request->all();
        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }
        $data['created_by'] = $user->id;
        $attraction = Attraction::create($data);

        $translationData = $request->all();
        $translationData['user_id'] = $user->id;
        $translationData['attraction_id'] = $attraction->id;
        $attraction->createTranslations($translationData, $language);
        
        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $attraction->saveFiles($data['tmp_files'], 'attractions/');
        }
        $attraction->load(['images', 'thumbnail']);
        if (!empty($attraction->images) && !empty($attraction->images[0])) {
            $attraction->images[0]->makeThumbnail();
        }
        

        $attraction->load(['translations']);

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
        
        $data = $request->all();
        $langId = $data['selected_language_id'];
        $language = Language::find($langId);
        if(!$language){
            return $this->responseNotFound();
        }
        
        if ($user->id != $attraction->user_id && !$user->hasEditorAccess()){
            return $this->responseForbidden();
        }

        $data['updated_by'] = $user->id;
        //Upload files
        if(!empty($data['tmp_files'])){
            $attraction->saveFiles($data['tmp_files'], 'attractions/');
        }
        if ($language->lang_code === Language::SR_CODE) {
            $attraction->update($data); //Update default values
        }
        $translationData = $request->all();
        $translationData['user_id'] = $user->id;
        $translationData['attraction_id'] = $attraction->id;
        $attraction->syncTranslations($translationData, $language);

        $attraction->load(['images', 'thumbnail', 'translations']);
        
        return $this->responseSuccess(AttractionResource::make($attraction));
    }

    /**
     * Create model translation
     * Not used
     *
     * @param CreateTranslationRequest $request
     * @param string $id
     * @param string $langId
     */
    public function createTranslation(CreateTranslationRequest $request, string $id, string $langId)
    {
        $user = auth()->user();

        $attraction = Attraction::find($id);
        if(!$attraction){
            return $this->responseNotFound();
        }
        
        if ($user->id != $attraction->user_id && !$user->hasEditorAccess()){
            return $this->responseForbidden();
        }

        $language = Language::find($langId);

        $data = $request->all();
        $data['created_by'] = $user->id;
        $data['attraction_id'] = $attraction->id;
        $data['language_id'] = $language->id;
        $data['lang_code'] = $language->lang_code;
        $translation = AttractionTranslation::create($data);
        $attraction->translateFromModel($translation);
        
        return $this->responseSuccess(AttractionResource::make($attraction));
    }

    /**
     * Update model translation. Used to create and update translation
     * Not used
     * @param UpdateTranslationRequest $request
     * @param string $id
     * @param string $translationId
     */
    public function updateTranslation(UpdateTranslationRequest $request, string $id, string $langId)
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
        $translation = AttractionTranslation::where('attraction_id', $id)->where('language_id', $langId)->first();
        if(!$translation){
            $language = Language::find($langId);
            $data['created_by'] = $user->id;
            $data['attraction_id'] = $attraction->id;
            $data['language_id'] = $language->id;
            $data['lang_code'] = $language->lang_code;
            $translation = AttractionTranslation::create($data);
        } else {
            $data['updated_by'] = $user->id;
            $translation->update($data);
        }
        $attraction->load('translations');
        $attraction->translateFromModel($translation);
        
        return $this->responseSuccess(AttractionResource::make($attraction));
    }
    
    /**
     * Delete model translation
     *
     * @param UpdateTranslationRequest $request
     * @param string $id
     * @param string $translationId
     */
    public function deleteTranslation(string $id, string $langId)
    {
        $user = auth()->user();

        $attraction = Attraction::find($id);
        if(!$attraction){
            return $this->responseNotFound();
        }
        $translation = AttractionTranslation::where('attraction_id', $id)->where('language_id', $langId)->first();
        if(!$translation){
            return $this->responseNotFound();
        }
        
        if ($user->id != $attraction->user_id && !$user->hasEditorAccess()){
            return $this->responseForbidden();
        }

        $translation->delete();
        
        return $this->responseSuccess();
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
