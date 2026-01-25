<?php

namespace Modules\Core\Http\Requests\V1\Region;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateRegionRequest
 * * Handles validation logic for updating an existing Region record.
 */
class UpdateRegionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to perform this action.
     * * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * * @return array
     */
    public function rules(): array
    {

        $regionId = $this->route('region') ? $this->route('region')->id : null;

        return [
            'name'      => ['sometime', 'string', 'max:100', Rule::unique('regions', 'name')->ignore($regionId)],
            'label'     => ['nullable', 'string', 'max:100'],
            'location.lat'  => ['required', 'numeric', 'between:-90,90'],
            'location.lng'  => ['required', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
