<?php
namespace App\Traits;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Transliterator;

/**
 * Handle logic for translating model
 */
trait Translates
{
    /**
     * Translate content to cyrilic
     * @return array
     */
    public function transleCyrillic($model): array
    {
        if (isset($model->translateFields)) {
            foreach ($model->translateFields as $fieldName) {
                $translations[$fieldName] = Transliterator::toCyrillic($this->$fieldName);
            }
            return $translations;
        }
        return false;
    }

    public function translateFromModel($model)
    {
        if (isset($model->translateFields)) {
            foreach ($model->translateFields as $field) {
                $this->{$field} = $model->{$field};
            }
            return true;
        }
        return false;
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

    public function translateModel($langId = null)
    {
        if ($langId) {
            $translation = $this->getTranslationByLangId($langId);
            if ($translation) {
                return $this->translateFromModel($translation);
            }
        }

        return false;
    }

    public function getAllLanguages()
    {
        return $this->translations()->with('language')->select(['id', 'language_id', 'lang_code'])->get();
    }

}
