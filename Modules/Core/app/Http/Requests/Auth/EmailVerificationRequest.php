<?php

namespace Modules\Core\Http\Requests\Auth;

use App\Facades\Logger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class EmailVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     *
     * In an API context, the user might not be authenticated when clicking the verification link
     * Therefore, we allow the request to proceed here, and the actual verification logic
     * (checking link validity and user matching) will be handled in the VerificationController
     * The 'signed' middleware also provides an initial layer of security
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request
     *
     * These rules primarily ensure that the 'id' and 'hash' parameters
     * required for verification are present in the request
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|numeric', // 'id' should typically be numeric (user ID)
            'hash' => 'required|string', // 'hash' is a string
        ];
    }

    /**
     * Get custom attributes for validator errors
     *
     * These custom attribute names will be used in validation messages
     * instead of the actual field names (e.g., 'id' becomes 'User ID')
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'id' => 'User ID',
            'hash' => 'Verification Hash',
        ];
    }

    /**
     * Get the error messages for the defined validation rules
     *
     * Custom error messages provide more user-friendly feedback
     * The :attribute placeholder will be replaced by the value defined in the attributes() method
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id.required' => 'The :attribute is missing from the verification link. Please ensure the link is complete',
            'id.numeric' => 'The :attribute in the link is invalid',
            'hash.required' => 'The :attribute is missing from the verification link. Please ensure the link is complete',
            'hash.string' => 'The :attribute in the link is invalid',
        ];
    }

    /**
     * Handle a failed validation attempt
     *
     * This method is overridden to return a JSON response for API requests
     * instead of redirecting, which is the default behavior for web requests
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {

        Logger::failedValidation(
            'email-verification-validation-check',
            'Failed Validation',
        );

        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed for verification link parameters',
            'errors' => $validator->errors()
        ], 422));
    }
}
