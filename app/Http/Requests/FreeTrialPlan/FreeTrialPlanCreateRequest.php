<?php

namespace App\Http\Requests\FreeTrialPlan;

use Illuminate\Foundation\Http\FormRequest;

class FreeTrialPlanCreateRequest extends FormRequest
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
            'name' => 'required|string|unique:free_trial_plans,name',
            'days' => 'nullable|numeric',
        ];
    }
}
