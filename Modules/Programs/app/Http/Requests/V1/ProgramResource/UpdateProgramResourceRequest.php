<?php

namespace Modules\Programs\Http\Requests\V1\ProgramResource;

use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Programs\Enums\V1\ResourceType;

/**
 * Class UpdateProgramResourceRequest
 * * Handles validation and authorization logic for updating an existing program resource.
 * This request ensures that only users with the 'resources.update' permission
 * can modify resource data and that all input conforms to business rules.
 * * @package Modules\Programs\Http\Requests\V1\ProgramResource
 */
class UpdateProgramResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * * Checks if the authenticated user has the required permission
     * to update program resources.
     * * @return bool True if the user is authorized, false otherwise.
     */
    public function authorize(): bool
    {
        return $this->user()->can('resources.update');
    }

    /**
     * Get the validation rules that apply to the request.
     * * Rules are defined for partial updates (optional fields), ensuring
     * data integrity for resource types, quantities, and costs.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The category of the resource.
             * Must be a valid value defined in the ResourceType Enum.
             */
            'resource_type' => [new Enum(ResourceType::class)],

            /**
             * The name of the resource.
             * Optional string, max length 255 characters.
             */
            'name'          => 'string|max:255',

            /**
             * The number of units.
             * Must be an integer and at least 1.
             */
            'quantity'      => 'integer|min:1',

            /**
             * The cost per unit.
             * Must be a numeric value and cannot be negative.
             */
            'cost'          => 'numeric|min:0',

            /**
             * Additional information or description.
             * Can be null or a string.
             */
            'notes'         => 'nullable|string'
        ];
    }
}
