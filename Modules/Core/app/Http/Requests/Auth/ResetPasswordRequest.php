<?php

namespace Modules\Core\Http\Requests\Auth;

use App\Facades\Logger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
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
            'token' => 'required|string',
            'email' => 'required|email:rfc|exists:users,email',
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'max:128',
                'confirmed',
            ],
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
            'token' => 'Password reset token',
            'email' => 'Email address',
            'password' => 'New password',
            'password_confirmation' => 'New password confirmation',
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
            'token.required' => 'The :attribute is missing',
            'email.required' => 'The :attribute is required',
            'email.email' => 'The :attribute must be a valid email address',
            'email.exists' => 'The :attribute provided does not exist in our records',
            'password.required' => 'The :attribute is required',
            'password.min' => 'The :attribute must be at least :min characters',
            'password.confirmed' => 'The :attribute confirmation does not match',
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
                'inputs' => $this->except(['password', 'password_confirmation', 'token'])
            ]
        );

        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed for password reset',
            'errors' => $validator->errors()
        ], 422));
    }
}
