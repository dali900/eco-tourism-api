<?php

namespace App\Http\Controllers;

use App\Models\Regulation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Contracts\RegulationRepositoryInterface;
use App\Http\Resources\Regulation\RegulationResource;
use App\Http\Resources\Regulation\RegulationGuestResource;
use App\Http\Resources\Regulation\RegulationResourcePaginated;
use App\Http\Resources\Regulation\RegulationTypeResource;

class RegulationController extends Controller
{
    /**
     * RegulationRepository
     *
     * @var RegulationRepository
     */
    private $regulationRepository;

    public function __construct(RegulationRepositoryInterface $regulationRepository) {
        $this->regulationRepository = $regulationRepository;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($app, $id)
    {
        //$rt = \App\Models\RegulationType::treeOf(function($q){$q->where('id',83);})->get()->toTree();
        $regulation = Regulation::with([
                'regulationType.ancestorsAndSelf' => fn ($query) => $query->orderBy('id', 'ASC'),
                'downloadFile', 
                'pdfFile', 
                'htmlFile', 
                'htmlFiles'
            ])->find($id);
        if(!$regulation){
            return $this->responseNotFound();
        }
        $user = auth()->user();
        if(!$user || (!$user->hasActivePlan($app) && !$user->hasAuthorAccess())){
            return $this->responseSuccess([
                'regulation' => RegulationGuestResource::make($regulation)
            ]);
        }
        
        return $this->responseSuccess([
            'regulation' => RegulationResource::make($regulation)
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
        $regulations = $this->regulationRepository->getAllFiltered($request->all(), $app);

        $regulations->with('regulationType');
        $regulationsPaginated = $regulations->paginate($perPage);
        
        $regulationResource = RegulationResourcePaginated::make($regulationsPaginated);
        return $this->responseSuccess([
            'regulations' => $regulationResource,
            'subtypes' => Regulation::getSubtypes()
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
                //'name' => 'required|string|unique:regulations,name,NULL,id,app,'.$app,
                'name' => [
                    'required', 
                    'string', 
                    // validate multiple unique columns
                    Rule::unique('regulations', 'name')
                        ->where('app', $app)
                ],
                'regulation_type_id' => 'required|numeric'
            ], 
            [
                //'required' => 'The :attribute field is required.',
                //'first_name.required' => 'We need to know your first name!', //for specific attribute
            ], 
            Regulation::attributeNames()
        );

        $user = auth()->user();

        $data = $request->all();
        $data['app'] = $app;
        $data['user_id'] = $user->id;
        $regulation = Regulation::create($data);
        
        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $regulation->saveFiles($data['tmp_files'], 'regulations/');
            $regulation->convertWordFileToPdf($data['tmp_files'], 'regulations/');
        }
        $regulation->load(['downloadFile', 'pdfFile', 'htmlFile', 'htmlFiles']);

        return $this->responseSuccess([
            'regulation' => RegulationResource::make($regulation)
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
        $user = auth()->user();
        
        $regulation = Regulation::find($id);
        if(!$regulation){
            return $this->responseNotFound();
        }
        
        if ($user->id != $regulation->user_id && !$user->hasEditorAccess()){
            return $this->responseForbidden();
        }
        
        $attr = $request->validate([
            'name' => [
                'required', 
                'string', 
                Rule::unique('regulations', 'name')
                    ->where('app', $app)
                    ->ignore($regulation->id)
            ],
            'regulation_type_id' => 'required|numeric'
        ]);

        $data = $request->all();
        $data['app'] = $app;
        $data['user_id'] = $user->id;
        //Upload files
        if(!empty($data['tmp_files'])){
            $regulation->saveFiles($data['tmp_files'], 'regulations/');
            $regulation->convertWordFileToPdf($data['tmp_files'], 'regulations/');
        }
        $regulation->update($data);
        $regulation->load(['downloadFile', 'pdfFile', 'htmlFile', 'htmlFiles']);
        
        return $this->responseSuccess([
            'regulation' => RegulationResource::make($regulation)
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
        $user = auth()->user();
        $regulation = Regulation::find($id);
        if(!$regulation){
            return $this->responseNotFound();
        }
        if ($user->id != $regulation->user_id && !$user->hasEditorAccess()){
            return $this->responseForbidden();
        }
        //delete file
        $regulation->deleteAllFiles();
        $regulation->delete();
        return $this->responseSuccess();
    }

    public function downloadFile($app, $id)
    {
        $regulation = Regulation::find($id);
        if(!$regulation){
            return $this->responseNotFound();
        }

        $filePath = storage_path("/app/".$regulation->downloadFile->file_path);
        //$downloadName = basename($filePath);
        $slug = Str::slug($regulation->name);
        $downloadName = $slug.'.'.$regulation->downloadFile->ext;
        return response()->download($filePath, $downloadName, ['Download-Name' => $downloadName, "Access-Control-Expose-Headers" => "Download-Name"]);
    }
}
