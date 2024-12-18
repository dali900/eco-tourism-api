<?php
namespace App\Traits;

use App\Models\File;
use App\Models\Language;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Turanjanin\SerbianTransliterator\Transliterator;

/**
 * Handle logic for translating model
 */
trait Translates
{
    /**
     * Translate content to cyrilic
     * @return array|null
     */
    public function getCyrillicTranslation($model)
    {
        if (isset($model->translateFields)) {
            foreach ($model->translateFields as $fieldName) {
                if ($this->$fieldName) {
                    $translations[$fieldName] = Transliterator::toCyrillic($this->$fieldName);
                }
            }
            return $translations;
        }
        return null;
    }
    
    /**
     * Translate content to latin
     * @return array|null
     */
    public function getLatinTranslation($model)
    {
        if (isset($model->translateFields)) {
            foreach ($model->translateFields as $fieldName) {
                if ($this->$fieldName) {
                    $translations[$fieldName] = Transliterator::toLatin($this->$fieldName);
                }
            }
            return $translations;
        }
        return null;
    }
    /**
     * Translate model fields.
     *
     * @param $model
     * @return Model|null
     */
    public function translateFromModel($model)
    {
        if (isset($model->translateFields)) {
            foreach ($model->translateFields as $field) {
                $this->{$field} = $model->{$field};
            }
            return true;
        }
        return null;
    }

    /**
     * Set model fields to empty string. Used on FE when selecting language with no translation
     *
     * @param Model $model
     * @return void
     */
    public function emptyModel($model)
    {
        if (isset($model->translateFields)) {
            foreach ($model->translateFields as $field) {
                $this->{$field} = "";
            }
        }
    }

    /**
     * Get translation by lang id
     *
     * @param integer $langId
     * @return Model|null
     */
    public function getTranslationByLangId(int $langId)
    {
        return $this->translations()->where('language_id', $langId)->first();
    }
    
    /**
     * Get translation by lang code
     *
     * @param string $langId
     * @return Model|null
     */
    public function getTranslationByLangCode(string $langId)
    {
        return $this->translations()->where('lang_code', $langId)->first();
    }

    /**
     * Search and apply translation for the model by language id
     *
     * @param int $langId
     * @return Model|null
     */
    public function translateModelByLangId($langId = null)
    {
        if ($langId) {
            $translation = $this->getTranslationByLangId($langId);
            if ($translation) {
                return $this->translateFromModel($translation);
            }
        }

        return null;
    }
    
    /**
     * Search and apply translation for the model by language code
     *
     * @param int $langId
     * @return Model|null
     */
    public function translateModelByLangCode($langCode = null)
    {
        if ($langCode) {
            $translation = $this->getTranslationByLangCode($langCode);
            if ($translation) {
                return $this->translateFromModel($translation);
            }
        }

        return null;
    }

    /**
     * Get all available languages for model.
     *
     * @return Collection|null
     */
    public function getAllLanguages()
    {
        return $this->translations()->with('language')->select(['id', 'language_id', 'lang_code'])->get();
    }

    public function createTranslations($translationData, $language){
        $userId = $translationData['user_id'];
        $originalLanguage = $language;
        //Create new translation
        $translationData['created_by'] = $userId;
        $translationData['language_id'] = $language->id;
        $translationData['lang_code'] = $language->lang_code;
        $translation = $this->translations()->create($translationData);

        //Create latin translation
        if ($language->lang_code === Language::SR_CODE) {
            $language = Language::findByCode(Language::SR_CYRL_CODE);
            $translationData = array_merge($translationData, $this->getCyrillicTranslation($translation));
            $translationData['language_id'] = $language->id;
            $translationData['lang_code'] = $language->lang_code;
            $this->translations()->create($translationData);
        } else if ($language->lang_code === Language::SR_CYRL_CODE) {
            //Create cyrillic translation
            $translation = $this->getTranslationByLangCode(Language::SR_CODE);
            $language = Language::findByCode(Language::SR_CYRL_CODE);
            $translationData = array_merge($translationData, $this->getCyrillicTranslation($translation));
            $translationData['language_id'] = $language->id;
            $translationData['lang_code'] = $language->lang_code;
            $this->translations()->create($translationData);
        } 
        
        if ($originalLanguage->lang_code !== Language::SR_CODE) {
            return $this->translateModelByLangId($originalLanguage->id);
        }
    }
    
    public function syncTranslations($translationData, $language){
        $userId = $translationData['user_id'];
        $translationData['updated_by'] = $userId;
        $translation = $this->getTranslationByLangId($language->id);
        //Update translation
        if ($translation) {
            $translation->update($translationData);
        } else {
            //Create new translation
            $translationData['created_by'] = $userId;
            $translationData['language_id'] = $language->id;
            $translationData['lang_code'] = $language->lang_code;
            $translation = $this->translations()->create($translationData);
        }

        //Update cuyrillic translation
        if ($language->lang_code === Language::SR_CODE) {
            $translation = $this->getTranslationByLangCode(Language::SR_CYRL_CODE);
            if($translation) {
                $translationData = $this->getCyrillicTranslation($translation);
                $translationData['updated_by'] = $userId;
                $translation->update($translationData);
            }
           
        } else if ($language->lang_code === Language::SR_CYRL_CODE) {
        //Update latin translation
            $translation = $this->getTranslationByLangCode(Language::SR_CODE);
            if($translation) {
                $translationData = $this->getLatinTranslation($translation);
                $translationData['updated_by'] = $userId;
                $translation->update($translationData);
            }
        } 

        return $this->translateModelByLangId($language->id);
    }

}
