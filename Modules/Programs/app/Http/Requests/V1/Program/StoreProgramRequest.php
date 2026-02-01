<?php

namespace Modules\Programs\Http\Requests\V1\Program;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Programs\Enums\V1\ProgramStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class StoreProgramRequest
 * * Handles the validation and authorization logic for creating a new Program.
 * This class ensures that incoming data complies with the system's integrity rules,
 * including unique naming, date sequencing, and enum-based status validation.
 * * @package Modules\Programs\Http\Requests\V1\Program
 */
class StoreProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * * Gatekeeping is performed via Spatie Permissions.
     * Required Permission: 'programs.create'
     * * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('programs.create');
    }

    /**
     * Get the validation rules that apply to the request.
     * * Constraints Summary:
     * - Name: Must be unique in the 'programs' table.
     * - Dates: 'start_date' cannot be in the past; 'end_date' must succeed 'start_date'.
     * - Status: Must strictly match values defined in ProgramStatus Enum.
     * - Objectives: Expects a structured array (stored as JSON in DB).
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'issue_category_id' => 'required|exists:issue_categories,id',
            'name'              => 'required|string|max:255|unique:programs,name',
            'description'       => 'nullable|string',
            'objectives'        => 'required|array',
            'target_groups'     => 'required|string',
            'start_date'        => 'required|date|after_or_equal:today',
            'end_date'          => 'required|date|after:start_date',
            'budget'            => 'required|numeric|min:0',
            'status'            => ['required', Rule::in(values: ProgramStatus::all())],
        ];
    }

    /**
     * Handle a failed validation attempt.
     * * Overrides the default Laravel behavior to return a consistent JSON response
     * structure instead of a redirect, which is essential for API consumers.
     * * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validation Errors',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
