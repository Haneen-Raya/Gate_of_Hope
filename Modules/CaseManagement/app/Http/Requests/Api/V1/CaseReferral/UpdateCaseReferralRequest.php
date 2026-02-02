<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\CaseReferral;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\CaseManagement\Enums\V1\CaseReferralDirection;
use Modules\CaseManagement\Enums\V1\CaseReferralStatus;
use Modules\CaseManagement\Enums\V1\CaseReferralType;
use Modules\CaseManagement\Enums\V1\CaseReferralUrgencyLevel;
use Modules\CaseManagement\Rules\CaseManagerOwnsCase;

class UpdateCaseReferralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('case.referral.update');
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'beneficiary_case_id' => ['nullable','integer','exists:beneficiary_cases,id',new CaseManagerOwnsCase()],
            'service_id'          => ['nullable','integer','exists:services,id'],
            'receiver_entity_id'  => ['nullable','integer','exists:entities,id'],
            'referral_type'       => ['nullable','string',Rule::in(CaseReferralType::all())],
            'direction'           => ['nullable','string',Rule::in(CaseReferralDirection::all())],
            'status'              => ['nullable','string',Rule::in(CaseReferralStatus::all())],
            'urgency_level'       => ['nullable','string',Rule::in(CaseReferralUrgencyLevel::all())],
            'reason'              => ['nullable', 'string', 'max:1000'],
            'notes'               => ['nullable', 'string', 'max:1000'],
            'followup_date'       => ['nullable', 'date','after_or_equal:referral_date'],
        ];
    }
}
