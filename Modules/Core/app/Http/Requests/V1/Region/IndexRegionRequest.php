<?php

namespace Modules\Core\Http\Requests\V1\Region;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class IndexRegionRequest
 * * This request handles the validation for listing regions.
 * It ensures that filtering parameters like spatial coordinates and search terms
 * are formatted correctly before reaching the Service or Builder.
 */
class IndexRegionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * * @return bool
     */
    public function authorize(): bool
    {
        return true; // Set to true to allow access, or add permission checks
    }

    /**
     * Get the validation rules that apply to the request.
     * * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'lat'       => ['nullable', 'numeric', 'between:-90,90'],
            'lng'       => ['nullable', 'numeric', 'between:-180,180'],
            'distance'  => ['nullable', 'numeric', 'min:1'], // Search radius in meters
            'is_active' => ['nullable', 'boolean'],
            'search'    => ['nullable', 'string', 'max:255'],
            'per_page'  => ['nullable', 'integer', 'min:1', 'max:100'],
            'page'      => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Custom error messages for the validation rules.
     * * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lat.between' => 'The latitude must be between -90 and 90 degrees.',
            'lng.between' => 'The longitude must be between -180 and 180 degrees.',
            'distance.min' => 'The search distance must be at least 1 meter.',
        ];
    }
}
