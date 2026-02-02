<?php

namespace Modules\Beneficiaries\Http\Requests\Api\V1\HousingType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateHousingTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('housing_types.update');
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'       => ['nullable','string','unique:housing_types,name','max:255'],
            'is_active'  => ['nullable','boolean'],
        ];
    }
}
