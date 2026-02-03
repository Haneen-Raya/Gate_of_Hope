<?php

namespace Modules\Programs\Http\Requests\V1\ActivitySession;

use Illuminate\Foundation\Http\FormRequest;

class NearbyActivitySessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for nearby activity sessions request.
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
     *
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'radius' => $this->radius ?? 5000,
        ]);
    }
}
