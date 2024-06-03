<?php
namespace App\Traits;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Handle logic for translating model
 */
trait Translates
{
    public function translateFromModel($model)
    {
        if (isset($model->translateFields)) {
            foreach ($model->translateFields as $field) {
                $this->{$field} = $model->{$field};
            }
        }
    }
    public function emptyModel($model)
    {
        if (isset($model->translateFields)) {
            foreach ($model->translateFields as $field) {
                $this->{$field} = "";
            }
        }
    }

    public function getTranslationByLangId(int $langId)
    {
        return $this->translations()->where('language_id', $langId)->first();
    }

    public function translateModel(int|null $langId)
    {
        if ($langId) {
            $translation = $this->getTranslationByLangId($langId);
            if ($translation) {
                $this->translateFromModel($translation);
            }
        }
    }

    public function getAllLanguages()
    {
        return $this->translations()->with('language')->select(['id', 'language_id', 'lang_code'])->get();
    }

}
