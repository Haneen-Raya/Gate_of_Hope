<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\Service;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CaseManagement\Enums\ServiceDirection;

class UpdateServiceRequest extends FormRequest
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
            'issue_category_id' => ['nullable','integer','exists:issue_categories,id'],
            'name'              => ['nullable', 'string', 'max:255'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'direction'         => ['nullable','string',Rule::in(ServiceDirection::all())],
            'unit_cost'         => ['nullable', 'integer'],
            'is_active'         => ['sometimes','boolean'],
        ];
    }
}
