<?php

namespace App\Models;

use App\Traits\ConvertFiles;
use App\Traits\Filterable;
use App\Traits\HandleFiles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regulation extends Model
{
    use HasFactory, Filterable, HandleFiles, ConvertFiles;

	const FOREIGN_TRADE_BUSINESS_VALUE = 'foreign_trade_business';
	const FOREIGN_EXCHANGE_BUSINESS_VALUE = 'foreign_exchange_business';
    const FOREIGN_CUSTOMS_BUSINESS_VALUE = 'foreign_customs_business';
    const FOREIGN_TRADE_BUSINESS_NAME = 'Spoljnotrgovinsko poslovanje';
	const FOREIGN_EXCHANGE_BUSINESS_NAME = 'Devizno poslovanje';
    const FOREIGN_CUSTOMS_BUSINESS_NAME = 'Carinsko poslovanje';
    
    protected $fillable = [
        'name',
        'user_id',
        'type_id',
        'created_at',
        'updated_at',
        'regulation_type_id',
        'file_path',
        'preview_file_path',
        'messenger',
        'maker',
        'validity_level',
        'start_date',
        'end_date',
        'use_start_date',
        'version_release_date',
        'version_end_date',
        'text_release_date',
        'text_start_date',
        'int_start_date',
        'user_id',
        'legal_basis',
        'basis',
        'invalid_basis',
        'invalid_regulation',
        'legal_predecessor_end_date',
        'historical_version',
        'note',
        'messenger_note',
        'approved',
        'app'
    ];

    public function sortable()
    {
        return [
            'name',
            'messenger',
            ['column' => 'user_name', 'type' => 'belongsTo', 'relation' => 'user', 'search_column' => 'name']
        ];
    }

    /**
     * Custom model filter
     *
     * @param array $field
     * @param Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    /* public function customFilter($field, $query)
    {
        $query->where('name', 'Botswana');
        return $query;
    } */

    /**
     * regulationType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function regulationType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RegulationType::class, 'regulation_type_id');
    }

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function attributeNames()
    {
        return [
            'name' => 'Naziv',
            'regulation_type_id' => 'Vrsta propisa',
        ];
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

    public static function getSubtypes()
	{
		return [
			['name' => self::FOREIGN_TRADE_BUSINESS_NAME, 'value' => self::FOREIGN_TRADE_BUSINESS_VALUE],
			['name' => self::FOREIGN_EXCHANGE_BUSINESS_NAME, 'value' => self::FOREIGN_EXCHANGE_BUSINESS_VALUE],
            ['name' => self::FOREIGN_CUSTOMS_BUSINESS_NAME, 'value' => self::FOREIGN_CUSTOMS_BUSINESS_VALUE]
		];
	}
}
