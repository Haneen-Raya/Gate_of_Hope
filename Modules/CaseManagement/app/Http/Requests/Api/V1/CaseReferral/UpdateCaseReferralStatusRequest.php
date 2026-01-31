<?php

namespace Modules\CaseManagement\Http\Requests\Api\V1\CaseReferral;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\CaseManagement\Enums\V1\CaseReferralStatus;

class UpdateCaseReferralStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->can('case.referral.update.status');
    }
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status'              => ['nullable','string',Rule::in(CaseReferralStatus::all())],
            'rejection_reason'    => ['nullable', 'string', 'max:1000'],
            'cancellation_reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

