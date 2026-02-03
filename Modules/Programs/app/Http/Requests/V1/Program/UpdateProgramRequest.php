<?php

namespace Modules\Programs\Http\Requests\V1\Program;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Programs\Enums\V1\ProgramStatus;

/**
 * Class UpdateProgramRequest
 * * Handles the validation and authorization for updating existing Program records.
 * This request supports partial updates by using the 'sometimes' rule, allowing
 * clients to submit only the fields they wish to change.
 * * @package Modules\Programs\Http\Requests\V1\Program
 */
class UpdateProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * * Access is granted based on the 'programs.update' permission.
     * Note: Further ownership checks are handled at the Policy level.
     * * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('programs.update');
    }

    /**
     * Get the validation rules that apply to the request.
     * * Key Logic:
     * - 'sometimes': Fields are only validated if they are present in the request.
     * - 'unique': The program name uniqueness check ignores the current record's ID
     * to prevent validation failure when the name remains unchanged.
     * - 'status': Always required during updates to ensure state transition logic
     * in the Service layer is triggered correctly.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'issue_category_id' => 'sometimes|exists:issue_categories,id',
            'name'              => 'sometimes|string|max:255|unique:programs,name,' . $this->route('program'),
            'status'            => ['required', Rule::in(values: ProgramStatus::all())],
            'end_date'          => 'sometimes|date|after:start_date',
            'budget'            => 'sometimes|numeric|min:0',
        ];
    }
}
