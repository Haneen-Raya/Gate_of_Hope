<?php

namespace Modules\Beneficiaries\Http\Requests\Api\V1\HousingType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateHousingTypeActivationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('housing_types.activation.update');
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
