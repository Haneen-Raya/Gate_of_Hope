<?php

namespace Modules\Beneficiaries\Http\Requests\Api\V1\HousingType;

use Illuminate\Foundation\Http\FormRequest;

class StoreHousingTypeRequest extends FormRequest
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
            'name'       => ['required','string','unique:housing_types,name','max:255'],
            'is_active'  => ['sometimes','boolean'],
        ];
    }
}
