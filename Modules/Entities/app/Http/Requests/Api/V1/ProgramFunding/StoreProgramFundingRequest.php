<?php

namespace Modules\Entities\Http\Requests\Api\V1\ProgramFunding;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class StoreProgramFundingRequest
 * * Validates the contractual and financial parameters for new program funding.
 * It enforces integrity constraints between donors and programs, ensuring that
 * funding periods are logically sound and amounts are positive.
 */
class StoreProgramFundingRequest extends FormRequest
{
    /**
     * Determine if the user has the authority to initiate funding records.
     * * @return bool
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user && $user->can('program.funding.create');
    }

    /**
     * Get the validation rules for funding allocation.
     * * RULES EXPLAINED:
     * - program_id/donor_entity_id: Must exist in their respective tables.
     * - start_date: Prevents backdating funding records (must be today or future).
     * - end_date: Strictly must occur after the start date to maintain a valid period.
     * - amount: Must be at least 1 unit of currency to be financially valid.
     * * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'program_id'      => ['required', 'integer', 'exists:programs,id'],
            'donor_entity_id' => ['required', 'integer', 'exists:entities,id'],
            'start_date'      => ['required', 'date', 'after_or_equal:today'],
            'end_date'        => ['required', 'date', 'after:start_date'],
            'currency'        => ['required', 'string', 'max:255'],
            'amount'          => ['required', 'integer', 'min:1'],
        ];
    }
}
