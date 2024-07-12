<?php

namespace App\Http\Requests\Ad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdCreateRequest extends FormRequest
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
            'order_num' => [
                'numeric', 'nullable', Rule::unique('ads', 'order_num')
            ],
            'category_id' => 'required|numeric',
            'selected_language_id' => 'required|numeric',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'currency' => 'required|string',
            'place_id' => 'required|numeric',
            'suggested' => 'nullable|boolean',
            'approved' => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'expires_at' => 'required|date',
            'note' => 'nullable|string|max:512',
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
            'title' => 'Naslov',
            'category_id' => 'Kategorija',
            'description' => 'Opis',
            'price' => 'Cena',
            'currency' => 'Valute',
            'place_id' => 'Mesto',
            'approved' => 'Odobren',
            'published_at' => 'Objavljen',
            'expires_at' => 'Ističe',
            'suggested' => 'Preporuka',
            'note' => 'Napomena',
            'order_num' => 'Redni broj za naj noviji sadržaj',
            'selected_language_id' => 'Izabrani jezik',
        ];
    }
}
