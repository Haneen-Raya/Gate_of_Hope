<?php

namespace Modules\Beneficiaries\Http\Requests\Api\V1\EducationLevel;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEducationLevelActivationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'is_active'  => ['required','boolean'],
        ];
    }
}
