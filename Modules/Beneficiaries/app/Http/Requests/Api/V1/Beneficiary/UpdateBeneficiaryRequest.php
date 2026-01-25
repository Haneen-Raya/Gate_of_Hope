<?php

namespace Modules\Beneficiaries\Http\Requests\Api\V1\Beneficiary;

use App\Rules\DuplicateExtensionCheck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Beneficiaries\Enums\V1\DisabilityType;
use Modules\Beneficiaries\Enums\V1\Gender;
use Modules\Beneficiaries\Enums\V1\Governorate;
use Modules\Beneficiaries\Enums\V1\ResidenceType;

/**
 * @class UpdateBeneficiaryRequest
 * 
 * Manages the validation logic for updating an existing beneficiary record.
 * 
 * Key Strategy: Uses the 'sometimes' rule to support PATCH-style updates, 
 * allowing the client to send only the fields that need modification.
 */
class UpdateBeneficiaryRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     * Authorization is typically handled via Policies in the Controller.
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Security & Integrity:
     * 1. Categorical fields (Enums) are validated only if present in the request.
     * 2. Identity File remains protected by strict MIME checks and double-extension prevention.
     * 3. National ID and Identity Hash are omitted here to prevent tampering with core identity 
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'governorate' => ['sometimes', 'string', Rule::in(Governorate::all())],
            'gender' => ['sometimes', 'string', Rule::in(Gender::all())],
            'date_of_birth' => 'sometimes|date',
            'address' => 'sometimes|string|max:255',
            'residence_type' => ['sometimes', 'string', Rule::in(ResidenceType::all())],
            'is_displaced' => 'nullable|boolean',
            'has_other_provider' => 'nullable|boolean',
            'original_hometown' => 'nullable|string|max:255',
            'disability_type' => ['sometimes', 'string', Rule::in(DisabilityType::all())],
            'identity_file' => [
                'sometimes',
                'file',
                'mimes:png,jpg,pdf',
                'mimetypes:image/png,image/jpeg,application/pdf',
                'max:10240',
                new DuplicateExtensionCheck(),
            ],
        ];
    }
}
