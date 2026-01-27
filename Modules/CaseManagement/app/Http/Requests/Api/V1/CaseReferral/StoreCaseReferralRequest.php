<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\CaseReferral;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CaseManagement\Enums\CaseReferralDirection;
use Modules\CaseManagement\Enums\CaseReferralStatus;
use Modules\CaseManagement\Enums\CaseReferralType;
use Modules\CaseManagement\Enums\CaseReferralUrgencyLevel;

class StoreCaseReferralRequest extends FormRequest
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
            'beneficiary_case_id' => ['required','integer','exists:beneficiary_cases,id'],
            'service_id'          => ['required','integer','exists:services,id'],
            'receiver_entity_id'  => ['required','integer','exists:entities,id'],
            'referral_type'       => ['required','string',Rule::in(CaseReferralType::all())],
            'direction'           => ['required','string',Rule::in(CaseReferralDirection::all())],
            'status'              => ['required','string',Rule::in(CaseReferralStatus::all())],
            'urgency_level'       => ['required','string',Rule::in(CaseReferralUrgencyLevel::all())],
            'reason'              => ['required', 'string', 'max:1000'],
            'notes'               => ['nullable', 'string', 'max:1000'],
            //'followup_date'       => ['required', 'date','after_or_equal:referral_date'],
        ];
    }
}
