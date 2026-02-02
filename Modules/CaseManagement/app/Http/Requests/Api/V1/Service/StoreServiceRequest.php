<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\Service;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\CaseManagement\Enums\V1\ServiceDirection;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('service.create');
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'issue_category_id' => ['required','integer','exists:issue_categories,id'],
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'direction'         => ['required','string',Rule::in(ServiceDirection::all())],
            'unit_cost'         => ['required', 'integer'],
            'is_active'         => ['sometimes','boolean'],
        ];
    }
}
