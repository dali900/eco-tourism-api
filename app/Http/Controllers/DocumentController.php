<?php

namespace App\Http\Controllers;

use App\Http\Resources\Document\DocumentResource;
use App\Contracts\DocumentRepositoryInterface;
use App\Http\Resources\Document\DocumentGuestResource;
use App\Http\Resources\Document\DocumentResourcePaginated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Document;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class DocumentController extends Controller
{
    /**
     * DocumentRepository
     *
     * @var DocumentRepository
     */
    private $documentRepository;

    public function __construct(DocumentRepositoryInterface $documentRepository) {
        $this->documentRepository = $documentRepository;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($app, $id)
    {
        $document = Document::with([
            'documentType.ancestorsAndSelf' => fn ($query) => $query->orderBy('id', 'ASC'),
            'downloadFile', 
            'pdfFile', 'htmlFile', 
            'htmlFiles'
        ])->find($id);
        if(!$document){
            return $this->responseNotFound();
        }

        $user = auth()->user();
        if(!$user || (!$user->hasActivePlan($app) && !$user->hasAuthorAccess())){
            return $this->responseSuccess([
                'document' => DocumentGuestResource::make($document)
            ]);
        }

        return $this->responseSuccess([
            'document' => DocumentResource::make($document)
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
        $documents = $this->documentRepository->getAllFiltered($request->all(), $app);
        $documents->with('documentType');
        $documentsPaginated = $documents->paginate($perPage);
        $documentsResource = DocumentResourcePaginated::make($documentsPaginated);
        return $this->responseSuccess([
            'documents' => $documentsResource
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
			'title' => 'required|string',
            'author' => 'required|string',
            'document_type_id' => 'required|numeric'
		]);

        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        $data['publish_date'] = !empty($data['publish_date']) ? $data['publish_date'] : date('Y-m-d H:i:s');

        $document = Document::create($data);

        //Save uploaded temp files
        if(!empty($data['tmp_files'])){
            $document->saveFiles($data['tmp_files'], 'documents/');
            $document->convertWordFileToPdf($data['tmp_files'], 'documents/');
        }

        $document->load(['downloadFile', 'pdfFile', 'htmlFile', 'htmlFiles']);
        return $this->responseSuccess([
            'document' => DocumentResource::make($document)
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
			'title' => 'required|string',
            'author' => 'required|string',
            'document_type_id' => 'required|numeric'
		]);

        $user = auth()->user();

        $document = Document::find($id);
        if(!$document){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        $data['publish_date'] = $data['publish_date'] ?? date('Y-m-d H:i:s');
        
        //Upload files
        if(!empty($data['tmp_files'])){
            $document->saveFiles($data['tmp_files'], 'documents/');
            $document->convertWordFileToPdf($data['tmp_files'], 'documents/');
        }

        $document->update($data);
        $document->load(['downloadFile', 'pdfFile', 'htmlFile', 'htmlFiles']);
        return $this->responseSuccess([
            'document' => DocumentResource::make($document)
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
        $document = Document::find($id);
        if(!$document){
            return $this->responseNotFound();
        }
        
        //delete files relation and run observer
        $document->deleteAllFiles();
        $document->delete();
        return $this->responseSuccess();
    }

    /**
     * Delete document file 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteFile($app, $id)
    {
        $document = Document::find($id);
        if(!$document){
            return $this->responseNotFound();
        }

        $documentPath = $document->downloadFile->filePath;
        if(Storage::exists($documentPath)){
            Storage::delete($documentPath);
            return $this->responseSuccess(['document' => DocumentResource::make($document)]);
        }
        return $this->responseSuccessMsg('File does not exist');
    }

    public function downloadFile($app, $id)
    {
        $document = Document::find($id);
        if(!$document){
            return $this->responseNotFound();
        }

        $filePath = storage_path("/app/".$document->downloadFile->file_path);
        $slug = Str::slug($document->title);
        $downloadName = $slug.'.'.$document->downloadFile->ext;
        return response()->download($filePath, $downloadName, ['Download-Name' => $downloadName, "Access-Control-Expose-Headers" => "Download-Name"]);
    }
}
