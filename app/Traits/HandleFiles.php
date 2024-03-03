<?php
namespace App\Traits;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Handle logic for moving and saving temp files
 */
trait HandleFiles
{
    /**
     * Files
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files()
    {
        return $this->morphMany(File::class, 'file_model');
    }
    
    /**
     * Move and save multipe temp files
     *
     * @param array $params
     * @param string $storageFolderPath 
     * @return self
     */
    public function saveFiles($files, $storageFolderPath)
    {
        $user = auth()->user();
        $modelFieldName = 'name';
        $modelClassName = basename(str_replace('\\', '/', get_class($this)));
        if(defined(get_class($this)."::FILE_NAME_WITH")){
            $modelFieldName = self::FILE_NAME_WITH;
        }
        $modelName = $this->{$modelFieldName} ?? $this->title ?? $modelClassName;
        $filesData = [];
        foreach ($files as $file) {
            $folderPath = $storageFolderPath;
            $fileTag = $file['file_tag'] ?? null;
            $isPublic = $file['is_public'] ?? false;
            if($isPublic){
                $folderPath = 'public/'.$folderPath;
            }
            $filePath = $file['file_path'];
            $extension = pathinfo(base_path().'/'.$filePath, PATHINFO_EXTENSION);
            $slug = Str::slug((strlen($modelName) > 20) ? substr($modelName, 0, 20) : $modelName);
            $fileName = $this->id.'_'.$slug.'_'.Str::random(6).'.'.$extension;
            //if it's image for html save with original name
            if($fileTag == File::TAG_HTML_PREVIEW && getimagesize(storage_path('app/'.$filePath))){
                $fileName = $file['original_name'];
            }
            $newPath = $folderPath.$fileName;
            Storage::move($filePath, $newPath);
            $filesData[] = [
                'original_name' => $file['original_name'],//original name
                'ext' => $extension,
                'file_path' => $newPath,
                'user_id' => $user->id,
                'is_tmp' => false,
                'file_tag' => $fileTag,
                'is_public' => $file['is_public'] ?? false
            ];
        }
        $this->files()->createMany($filesData);
        return $this;
    }

    /**
     * Move and save temp file
     *
     * @param array $file
     * @return self
     */
    public function saveFile($file, $folderPath)
    {
        $user = auth()->user();
        $modelFieldName = 'name';
        $filePath = $file['file_path'];
        $isPublic = $file['is_public'] ?? false;
        $fileTag = $file['file_tag'] ?? null;
        $modelClassName = basename(str_replace('\\', '/', get_class($this)));
        if($isPublic){
            $folderPath = 'public/'.$folderPath;
        }
        if(defined(get_class($this)."::FILE_NAME_WITH")){
            $modelFieldName = self::FILE_NAME_WITH;
        }
        $modelName = $this->{$modelFieldName} ?? $this->title ?? $modelClassName;
        $fileData = [];

        $extension = pathinfo(base_path().'/'.$filePath, PATHINFO_EXTENSION);
        $slug = Str::slug((strlen($modelName) > 20) ? substr($modelName, 0, 20) : $modelName);
        $fileName = $this->id.'_'.$slug.'_'.Str::random(6).'.'.$extension;
        //if it's image for html save with original name
        if($fileTag == File::TAG_HTML_PREVIEW && getimagesize(storage_path('app/'.$filePath))){
            $fileName = $file['original_name'];
        }
        $newPath = $folderPath.$fileName;
        Storage::move($filePath, $newPath);
        $fileData[] = [
            'original_name' => $file['original_name'],
            'ext' => $extension,
            'file_path' => $newPath,
            'user_id' => $user->id,
            'is_tmp' => false,
            'file_tag' => $file['file_tag'] ?? null,
            'is_public' => $file['is_public'] ?? false
        ];
        $this->files()->createMany($fileData);
        return $this;
    }

    /**
     * Delete all files and table rows
     *
     * @return void
     */
    public function deleteAllFiles()
    {
        foreach ($this->files as $file){
            $filePath = $file->file_path;
            //delete file
            if(!empty($filePath)){
                Storage::delete($filePath);
            }
            $file->delete();
        }
    }
    
    /**
     * Delete all html files and table rows
     *
     * @return void
     */
    public function deleteAllHtmlFiles()
    {
        foreach ($this->htmlFiles as $file){
            $filePath = $file->file_path;
            //delete file
            if(!empty($filePath)){
                Storage::delete($filePath);
            }
            $file->delete();
        }
    }

}
