<?php

namespace Modules\Beneficiaries\Http\Requests\Beneficiary;

use App\Rules\DuplicateExtensionCheck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Beneficiaries\Enums\V1\DisabilityType;
use Modules\Beneficiaries\Enums\V1\Gender;
use Modules\Beneficiaries\Enums\V1\Governorate;
use Modules\Beneficiaries\Enums\V1\ResidenceType;

/**
 * @class StoreBeneficiaryRequest
 * 
 * Handles the validation and data preparation for creating a new beneficiary.
 * It enforces integrity through Enum checks, security through ID hashing,
 * and robust file validation to prevent malicious uploads.
 */
class StoreBeneficiaryRequest extends FormRequest
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
     * Prepare data for validation.
     * 
     * Logic:
     * 1. Hashes the raw 'national_id' using SHA256 to create a unique 'identity_hash'.
     * 2. This allow us to check for duplicates in the database without storing 
     * plain-text IDs in a searchable index, enhancing data privacy.
     */
    protected function prepareForValidation()
    {
        if ($this->filled('national_id')) {
            $this->merge([
                'identity_hash' => hash('sha256', $this->input('national_id'))
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Validation Strategy:
     * - Categorical Data: Enforced via Enum `all()` methods to ensure domain integrity.
     * - Privacy & Security: Hashed ID uniqueness check to prevent double registration.
     * - File Security: Strict MIME check and custom DuplicateExtensionCheck to block double-extension attacks.
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'governorate' => ['required', 'string', Rule::in(Governorate::all())],
            'gender' => ['required', 'string', Rule::in(Gender::all())],
            'date_of_birth' => 'required|date|before:today',
            'address' => 'required|string|max:255',
            'residence_type' => ['required', 'string', Rule::in(ResidenceType::all())],
            'is_displaced' => 'nullable|boolean',
            'has_other_provider' => 'nullable|boolean',
            'original_hometown' => 'nullable|string|max:255',
            'disability_type' => ['required', 'string', Rule::in(DisabilityType::all())],
            'identity_hash' => [
                'required',
                Rule::unique('beneficiaries', 'identity_hash')
            ],
            'national_id' => 'required|string|max:20',
            'identity_file' => [
                'required',
                'file',
                'mimes:png,jpg,pdf',
                'mimetypes:image/png,image/jpeg,application/pdf',
                'max:10240',
                new DuplicateExtensionCheck(),
            ],
        ];
    }

    /**
     * Custom attribute names for validation errors.
     * 
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'identity_hash' => 'National ID',
            'identity_file' => 'Identity Document',
            'date_of_birth' => 'Date of Birth',
        ];
    }
}
