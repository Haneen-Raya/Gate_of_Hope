<?php

namespace Modules\Beneficiaries\Http\Requests\EmploymentStatus;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmploymentStatusRequest extends FormRequest
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
            'name'       => ['required','string','unique:employment_statuses,name','max:255'],
            'is_active'  => ['sometimes','boolean'],
        ];
    }
}
