<?php

namespace Modules\Entities\Http\Requests\Api\V1\ProgramFunding;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class UpdateProgramFundingRequest
 * * Facilitates modifications to existing funding agreements.
 * Supports partial updates (PATCH) while maintaining strict cross-field validation
 * for dates and financial amounts.
 */
class UpdateProgramFundingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to modify financial records.
     * * @return bool
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user && $user->can('program.funding.update');
    }

    /**
     * Get the validation rules for updating funding records.
     * * Note: Fields are nullable to support partial updates without losing data integrity.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'program_id'      => ['nullable', 'integer', 'exists:programs,id'],
            'donor_entity_id' => ['nullable', 'integer', 'exists:entities,id'],
            'start_date'      => ['nullable', 'date', 'after_or_equal:today'],
            'end_date'        => ['nullable', 'date', 'after:start_date'],
            'currency'        => ['nullable', 'string', 'max:255'],
            'amount'          => ['nullable', 'integer', 'min:1'],
        ];
    }
}
