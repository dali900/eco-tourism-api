<?php

namespace App\Http\Requests\Attraction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttractionCreateRequest extends FormRequest
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
                'required', 'string', Rule::unique('attractions', 'name')
            ],
            'order_num' => [
                'numeric', 'nullable', Rule::unique('attractions', 'order_num')
            ],
            'category_id' => 'required|numeric',
            'summary' => 'required|string',
            'content' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.unique' => 'Korisnik već poseduje pretplatu.',
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
            'category_id' => 'Kategorija',
            'summary' => 'Sažetak',
            'content' => 'Sadržaj',
            'order_num' => 'Redni broj za naj noviji sadržaj'
        ];
    }
}
