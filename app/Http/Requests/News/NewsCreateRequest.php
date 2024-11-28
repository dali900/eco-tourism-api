<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => [
                'required', 'string', 'max:256'
            ],
            'subtitle' => [
                'nullable', 'max:256'
            ],
            'summary' => [
                'nullable', 'string'
            ],
            'text' => [
                'required', 'string'
            ],
            'category_ids' => [
                'required', 'array', 'min:1'
            ],
            'category_ids.*' => [
                'required', 'numeric'
            ],
            'selected_language_id' => [
                'required', 'numeric'
            ]
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'Naslov',
            'subtitle' => 'Podnaslov',
            'summary' => 'Sažetak',
            'text' => 'Tekst',
            'category_ids' => 'Kategorija',
            'selected_language_id' => 'Izabrani jezik',
        ];
    }
}
