<?php

namespace App\Http\Requests\Attraction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttractionCategoryCreateRequest extends FormRequest
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
            'name' => [
                'required', 'string', 'max:128', Rule::unique('attraction_categories', 'name')
            ],
            'parent_id' => 'nullable|numeric',
            'selected_language_id' => 'required|numeric',
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
            'name' => 'Naziv',
            'parent_id' => 'Nadkategorija',
            'selected_language_id' => 'Izabrani jezik',
        ];
    }
}
