<?php

namespace Modules\Programs\Http\Requests\V1\ProgramResource;

use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Programs\Enums\V1\ResourceType;

/**
 * Class StoreProgramResourceRequest
 * * Handles validation and authorization for creating new program resources.
 * This class ensures that the program exists, the resource type is valid,
 * and all mandatory financial and quantitative data are provided.
 * * @package Modules\Programs\Http\Requests\V1\ProgramResource
 */
class StoreProgramResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * * Verifies if the authenticated user possesses the 'resources.allocate' permission
     * required to bind new resources to a program.
     * * @return bool True if authorized, false otherwise.
     */
    public function authorize(): bool
    {
        return $this->user()->can('resources.allocate');
    }

    /**
     * Get the validation rules that apply to the request.
     * * Enforces strict rules:
     * - program_id: Must exist in the programs table.
     * - resource_type: Must match defined Enum cases.
     * - financials: Quantity and cost must be positive values.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * The ID of the target program.
             * Required and must exist in the 'programs' table 'id' column.
             */
            'program_id'    => 'required|exists:programs,id',

            /**
             * The category of the resource.
             * Required and must be a valid ResourceType Enum value.
             */
            'resource_type' => ['required', new Enum(ResourceType::class)],

            /**
             * The display name of the resource.
             * Required string, limited to 255 characters.
             */
            'name'          => 'required|string|max:255',

            /**
             * Total number of units to allocate.
             * Required integer, minimum value of 1.
             */
            'quantity'      => 'required|integer|min:1',

            /**
             * Price per unit of the resource.
             * Required numeric value (decimal allowed), minimum 0.
             */
            'cost'          => 'required|numeric|min:0',

            /**
             * Optional descriptive notes.
             * Can be null or a string.
             */
            'notes'         => 'nullable|string'
        ];
    }
}
