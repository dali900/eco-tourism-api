<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsCategoryUpdateRequest extends FormRequest
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
                'required', 'string', 'max:256'
            ],
            'parent_id' => [
                'nullable', 'numeric'
            ],
            'visible' => [
                'nullable', 'numeric'
            ],
            'order_num' => [
                'nullable', 'numeric'
            ],
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
            'parent_id' => 'Pripada',
            'visible' => 'Vidljiv',
            'order_num' => 'Redni broj',
        ];
    }

}
