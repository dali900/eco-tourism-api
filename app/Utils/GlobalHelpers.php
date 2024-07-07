<?php


use App\Models\Language;

/**
 * Get selected language from request or return default
 */
if (! function_exists('getLnaguageId')) {
    function getLnaguageId($request, $defaultLangId = null) {
        $langId = $request->input('langId');
        if (!$langId) {
            if ($defaultLangId) return $defaultLangId;
            return Language::findByCode(config('app.locale'))->id;
        }
        return $langId;
    }
}

/**
 * Get selected or default language
 */
if (! function_exists('getSelectedOrDefaultLangId')) {
    function getSelectedOrDefaultLangId($selectedLangId = null) {
        if (!$selectedLangId) {
            return Language::findByCode(config('app.locale'))->id;
        }
        return $selectedLangId;
    }
}

/**
 * Format money
 */
if (! function_exists('formatMoney')) {
    function formatMoney($amount = 0) {
        // Round up the amount to the nearest whole number
        $amount = ceil($amount);

        // Format the amount as money without decimal places
        return number_format($amount, 0, ',', '.');
    }
}