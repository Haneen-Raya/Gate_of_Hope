<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\CaseReferral;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CaseManagement\Enums\CaseReferralDirection;
use Modules\CaseManagement\Enums\CaseReferralStatus;
use Modules\CaseManagement\Enums\CaseReferralType;
use Modules\CaseManagement\Enums\CaseReferralUrgencyLevel;

class UpdateCaseReferralRequest extends FormRequest
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
            'beneficiary_case_id' => ['nullable','integer','exists:beneficiary_cases,id'],
            'service_id'          => ['nullable','integer','exists:services,id'],
            'receiver_entity_id'  => ['nullable','integer','exists:entities,id'],
            'referral_type'       => ['nullable','string',Rule::in(CaseReferralType::all())],
            'direction'           => ['nullable','string',Rule::in(CaseReferralDirection::all())],
            'status'              => ['nullable','string',Rule::in(CaseReferralStatus::all())],
            'urgency_level'       => ['nullable','string',Rule::in(CaseReferralUrgencyLevel::all())],
            'reason'              => ['nullable', 'string', 'max:1000'],
            'notes'               => ['nullable', 'string', 'max:1000'],
            'referral_date'       => ['nullable', 'date'],
            'followup_date'       => ['nullable', 'date','after_or_equal:referral_date'],
            'accepted_at'         => ['nullable', 'date','after_or_equal:referral_date'],
            'completed_at'        => ['nullable', 'date','after_or_equal:accepted_at'],
            'rejected_at'         => ['nullable', 'date','after_or_equal:referral_date'],
            'cancelled_at'        => ['nullable', 'date','after_or_equal:referral_date'],
            'rejection_reason'    => ['nullable', 'string', 'max:1000'],
            'cancellation_reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
