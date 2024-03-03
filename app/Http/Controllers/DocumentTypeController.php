<?php

namespace App\Http\Controllers;

use App\Contracts\DocumentTypeRepositoryInterface;
use App\Http\Resources\Document\DocumentTypeResource;
use App\Http\Resources\Document\DocumentTypeResourcePaginated;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    /**
     * DocumentTypeRepository
     *
     * @var DocumentTypeRepositoryInterface
     */
    private $documentTypeRepository;

    public function __construct(DocumentTypeRepositoryInterface $documentTypeRepository) {
        $this->documentTypeRepository = $documentTypeRepository;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($app, $id)
    {
        $documentType = DocumentType::find($id);
        if(!$documentType){
            return $this->responseNotFound();
        }
        return $this->responseSuccess([
            'document' => DocumentTypeResource::make($documentType)
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
        $documentTypes = $this->documentTypeRepository->getAllFiltered($request->all(), $app);
        //$documents->with('user');
        $documentTypes->with('parent');
        $documentTypesPaginated = $documentTypes->paginate($perPage);
        return $this->responseSuccess([
            'document_types' => DocumentTypeResourcePaginated::make($documentTypesPaginated)
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getRoots(Request $request, $app)
    {
        return $this->responseSuccess([
            'document_root_types' => DocumentType::whereApp($app)->whereNull('parent_id')->get()
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
            'name' => 'required|string',
            'parent_id' => 'nullable|numeric'
		]);
        
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;

        $documentType = DocumentType::create($data);
        $documentType->load('parent');
        return $this->responseSuccess([
            'document_type' => DocumentTypeResource::make($documentType)
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
            'name' => 'required|string'
		]);
       
        $user = auth()->user();

        $documentType = DocumentType::find($id);
        if(!$documentType){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        if(!$documentType->update($data)){
            return $this->responseErrorSavingModel();
        }
        $documentType->load('parent');
        
        return $this->responseSuccess([
            'document_type' => DocumentTypeResource::make($documentType)
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
        $documentType = DocumentType::find($id);
        if(!$documentType){
            return $this->responseNotFound();
        }
        $documentType->delete();
        return $this->responseSuccess();
    }
}
