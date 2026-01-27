<?php

namespace Modules\Entities\Http\Requests\Api\V1\ProgramFunding;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramFundingRequest extends FormRequest
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
            'program_id'      => ['required','integer','exists:programs,id'],
            'donor_entity_id' => ['required','integer','exists:entities,id'],
            'start_date'      => ['required', 'date','after_or_equal:today'],
            'end_date'        => ['required', 'date','after:start_date'],
            'currency'        => ['required', 'string', 'max:255'],
            'amount'          => ['required', 'integer', 'min:1'],
        ];
    }
}

