<?php

namespace Modules\Core\Http\Requests\Auth;

use App\Facades\Logger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email:rfc|exists:users,email',
        ];
    }

    /**
     * Get custom attributes for validator errors
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => 'Email address',
        ];
    }

    /**
     * Get the error messages for the defined validation rules
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'The :attribute is required',
            'email.email' => 'The :attribute must be a valid email address',
            'email.exists' => 'The :attribute provided does not exist in our records',
        ];
    }

    /**
     * Handle a failed validation attempt
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {

        Logger::failedValidation(
            'reset-password-validation-check',
            'Failed Validation',
            [
                'inputs' => $this->all()
            ]
        );

        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed for password reset request',
            'errors' => $validator->errors()
        ], 422));
    }
}
