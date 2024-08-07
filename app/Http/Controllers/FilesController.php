<?php

namespace App\Http\Controllers;

use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    /**
     * Upload file to temp folder
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        //$path = $file->store('tmp'); //the MIME type is somehow confusing Laravel
        $hash = Str::random(40);
        $extension = $request->file('file')->getClientOriginalExtension();
        $path = $file->storeAs('tmp', $hash . '.' . $extension);
        return $this->responseSuccess([
            'file_path' => $path,
        ]);
    }

    /**
     * UploadMultiple file to temp folder
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadMultiple(Request $request)
    {
        $files = $request->file('files');
        $file_tag = $request->input('file_tag') ?? File::TAG_IMAGE_FILE;
        $is_public = $request->has('is_public') ? $request->input('is_public') : 1;
        $filesCollection = collect();
        foreach ($files as $file) {
            //$path = $file->store('tmp'); //the MIME type is somehow confusing Laravel
            $hash = Str::random(40);
            $extension = $file->getClientOriginalExtension();
            $path = $file->storeAs('tmp', $hash . '.' . $extension);
            $fileModel = new File();
            $fileModel->file_path = $path;
            $fileModel->original_name = $file->getClientOriginalName();
            $fileModel->is_tmp = true;
            $fileModel->file_tag = $file_tag;
            $fileModel->is_public = (bool)$is_public;
            if ($file_tag === File::TAG_IMAGE_FILE) {
                $fileModel->resizeImageToMaxWidth();
            }
            $filesCollection->push($fileModel);


        }
        return $this->responseSuccess(FileResource::collection($filesCollection));
    }

    public function createThumbnail(Request $request, $fileId)
    {
        $file = File::find($fileId);
        if (!$file){
            return $this->responseNotFound();
        }
        if ($file->file_tag !== 'image') {
            return $this->responseValidationError(['message' => 'File is not an image']);
        }

        if ($file->fileModel->thumbnail) {
            $file->fileModel->thumbnail->deleteFile();
        }
        $thumbnailFile = $file->makeThumbnail();

        return $this->responseSuccess(FileResource::make($thumbnailFile));
    }

    /**
     * Delete file from storage
     *
     * @param  string  $filePath
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        //TODO: staviti middleware u route
        $user = auth()->user();
        if(!$user || $user->getAccessLevel() < User::ROLE_LEVEL_ADMIN){
            return $this->responseForbidden();
        }
        $request->validate([
            'file_path' => 'required|string',
        ]);

        $filePath = $request->file_path;
        $file = File::where('file_path', $filePath)->first();
        if(!$file){
            return $this->responseNotFound();
        }
        if ($file->fileModel->thumbnail) {
            $file->fileModel->thumbnail->deleteFile();
        }
        $file->delete();
        if(Storage::exists($filePath)){
            Storage::delete($filePath);
            return $this->responseSuccess();
        }
        return $this->responseSuccessMsg('File does not exist');
    }
    
    /**
     * Delete file from storage
     *
     * @param  string  $filePath
     * @return \Illuminate\Http\Response
     */
    public function deleteTmpFile(Request $request)
    {
        
        $request->validate([
            'file_path' => 'required|string',
        ]);

        $filePath = $request->file_path;
        if(Storage::exists($filePath)){
            Storage::delete($filePath);
            return $this->responseSuccess();
        }
        return $this->responseSuccessMsg('File does not exist');
    }
}
