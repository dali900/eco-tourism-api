<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionCreateRequest extends FormRequest
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
        $start_date_before_or_equal = "";
        if (!empty($this->end_date)){
            $start_date_before_or_equal = '|before_or_equal:'.$this->end_date;
        }

        return [
            'subscription_plan_id' => 'required|numeric',
            //'user_id' => 'required|numeric|unique:subscriptions,user_id',
            'user_id' => 'required|numeric',
            'start_date' => 'nullable|date'.$start_date_before_or_equal,
            'end_date' => 'nullable|date',
            'status' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.unique' => 'Korisnik već poseduje pretplatu.',
        ];
    }
}
