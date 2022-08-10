<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'amount_required' => ['required', 'numeric', 'min:1'],
            'terms_in_week' => ['required', 'numeric', 'min:1', $this->mustNotBeDecimal()],
        ];
    }

    private function mustNotBeDecimal()
    {
        return function ($attribute, $value, $fail) {
            if (str_contains($value, '.')) {
                $fail('Terms in week must be an integer.');
            }
        };
    }

    private function mustBePositive()
    {
        return function ($attribute, $value, $fail) {
            if ($value < 1) {
                $fail('Terms in week must be greater than or equal to 1.');
            }
        };
    }
}
