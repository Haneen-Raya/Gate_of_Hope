<?php

namespace Modules\Core\Http\Requests\V1\Region;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreRegionRequest
 * * Handles validation logic for creating a new Region record.
 */
class StoreRegionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to perform this action.
     * * @return bool
     */
    public function authorize(): bool
    {
        // Typically checks for permissions like 'create regions'
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * * @return array
     */
    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:100', Rule::unique('regions', 'name')],
            'label'     => ['nullable', 'string', 'max:100'],
            'location.lat'  => ['required', 'numeric', 'between:-90,90'],
            'location.lng'  => ['required', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
