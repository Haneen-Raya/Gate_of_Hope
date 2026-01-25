<?php

namespace Modules\Beneficiaries\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
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
        'name'      => ['sometimes','string','max:100'],
        'is_active' => ['sometimes','boolean'],
        ];
    }
}
