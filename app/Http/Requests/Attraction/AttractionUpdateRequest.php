<?php

namespace App\Http\Requests\Attraction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttractionUpdateRequest extends FormRequest
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
                'required', 
                'string', 
                Rule::unique('attractions', 'name')
                    ->ignore($this->id)
            ],
            'category_id' => 'required|numeric',
            'summary' => 'required|string',
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
        ];
    }

}
