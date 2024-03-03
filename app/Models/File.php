<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    const TAG_HTML_PREVIEW = 'html-preview';
    const TAG_PDF_PREVIEW = 'pdf-preview';
    const TAG_DOWNLOAD_FILE = 'download-file';

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
