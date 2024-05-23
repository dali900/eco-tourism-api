<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class File extends Model
{
    use HasFactory;

    const TAG_HTML_PREVIEW = 'html-preview';
    const TAG_PDF_PREVIEW = 'pdf-preview';
    const TAG_DOWNLOAD_FILE = 'download-file';
    const TAG_IMAGE_FILE = 'image';
    const TAG_IMAGE_THUMBNAIL = 'image-thumbnail'; //W:360 - H:270

    protected $fillable = [
        'original_name',
        'file_model_id',
        'file_model_type',
        'file_path',
        'ext',
        'user_id',
        'is_tmp',
        'file_tag',
        'is_public',
        'active',
        'status',
    ];

    public function fileModel()
    {
        return $this->morphTo('file_model');
    }

    public function makeThumbnail()
    {
        $fileName = pathinfo($this->file_path, PATHINFO_FILENAME);
        $fileExt = pathinfo($this->file_path, PATHINFO_EXTENSION);
        $fileDir = pathinfo($this->file_path, PATHINFO_DIRNAME);
        $thumbnailPath = $fileDir.'/'.$fileName."-thumbnail_".$this->id.'.'.$fileExt;
        Storage::copy($this->file_path, $thumbnailPath);
        // create new image instance
        logger(storage_path('app/'.$thumbnailPath));
        $image = ImageManager::imagick()->read(storage_path('app/'.$thumbnailPath));
        $image->scale(height: 330);
        $image->save();

        $fileModel = new File();
        $fileModel->file_path = $thumbnailPath;
        $fileModel->original_name = $this->original_name;
        $fileModel->is_tmp = 0;
        $fileModel->file_tag = File::TAG_IMAGE_THUMBNAIL;
        $fileModel->is_public = 1;
        $fileModel->file_model_id = $this->file_model_id;
        $fileModel->file_model_type = $this->file_model_type;
        $fileModel->save();
        return $fileModel;
    }

    /**
     * Deletes model and the actual file
     *
     * @return void
     */
    public function deleteFile()
    {
        if(!empty($this->file_path)){
            Storage::delete($this->file_path);
        }
        $this->delete();
    }
}
