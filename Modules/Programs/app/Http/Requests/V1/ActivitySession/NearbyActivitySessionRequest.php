<?php

namespace Modules\Programs\Http\Requests\V1\ActivitySession;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class NearbyActivitySessionRequest
 * * * This request is used for Location-Based Services (LBS).
 * It validates input for fetching activity sessions within a specific geographic radius.
 * * @package Modules\Programs\Http\Requests\V1\ActivitySession
 */
class NearbyActivitySessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * * Current setting: Public/Global access (returns true).
     * This may be restricted in the future based on user roles or API tokens.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for nearby activity sessions request.
     * * * Constraints:
     * - lat: Must be a valid Latitude coordinate (-90 to 90).
     * - lng: Must be a valid Longitude coordinate (-180 to 180).
     * - radius: Search distance in meters (Min: 100m, Max: 50km).
     * - activity_id: Optional filter to narrow down sessions by a specific activity.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'lat'         => ['required', 'numeric', 'between:-90,90'],
            'lng'         => ['required', 'numeric', 'between:-180,180'],
            'radius'      => ['sometimes', 'integer', 'min:100', 'max:50000'],
            'activity_id' => ['sometimes', 'integer', 'exists:activities,id'],
        ];
    }

    /**
     * Prepare data for validation.
     * * * Defaulting Logic:
     * If the 'radius' is not provided by the client, it defaults to 5000 meters (5km)
     * to ensure the spatial query always has a defined boundary.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'radius' => $this->radius ?? 5000,
        ]);
    }
}
