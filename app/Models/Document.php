<?php

namespace App\Models;

use App\Traits\ConvertFiles;
use App\Traits\HandleFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory, HandleFiles, ConvertFiles;

    /**
     * Include model name/title in file name
     */
    const FILE_NAME_WITH = "title";

    protected $fillable = [
        'title',
        'author',
        'user_id',
        'text',
        'comment',
        'publish_date',
        'document_type_id',
        'file_path',
        'preview_file_path',
        'approved',
        'app'
    ];

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * documentType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function documentType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }
    
    /**
     * All Files
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function htmlFiles()
    {
        return $this->morphMany(File::class, 'file_model')->where('file_tag', File::TAG_HTML_PREVIEW);
    }
    
    public function htmlFile()
    {
        return $this->morphOne(File::class, 'file_model')->where('file_tag', File::TAG_HTML_PREVIEW)->where('ext', 'html')->latest();
    }
    
    /**
     * Files
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function pdfFile()
    {
        return $this->morphOne(File::class, 'file_model')->where('file_tag', File::TAG_PDF_PREVIEW);
    }
    
    /**
     * Files
     * should use with filte_tag File::TAG_DOWNLOAD_FILE
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function downloadFile()
    {
        return $this->morphOne(File::class, 'file_model');
    }
}
