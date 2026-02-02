<?php

namespace Modules\Entities\Http\Requests\Api\V1\ProgramFunding;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramFundingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'program_id'      => ['nullable','integer','exists:programs,id'],
            'donor_entity_id' => ['nullable','integer','exists:entities,id'],
            'start_date'      => ['nullable', 'date','after_or_equal:today'],
            'end_date'        => ['nullable', 'date','after:start_date'],
            'currency'        => ['nullable', 'string', 'max:255'],
            'amount'          => ['nullable', 'integer', 'min:1'],
        ];
    }
}
