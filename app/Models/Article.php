<?php

namespace App\Models;

use App\Traits\ConvertFiles;
use App\Traits\HandleFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory, HandleFiles, ConvertFiles;

    protected $fillable = [
        'title',
        'user_id',
        'author',
        'publish_date',
        'file_path',
        'preview_file_path',
        'article_type_id',
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function downloadFile()
    {
        return $this->morphOne(File::class, 'file_model')->where('file_tag', File::TAG_DOWNLOAD_FILE);
    }

    /**
     * articleType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function articleType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ArticleType::class, 'article_type_id');
    }
}
