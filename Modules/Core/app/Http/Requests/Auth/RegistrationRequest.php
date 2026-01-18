<?php

namespace Modules\Core\Http\Requests\Auth;

use App\Facades\Logger;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class RegistrationRequest extends FormRequest
{

    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'user_type' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'max:255',
                'email',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),

                'max:128',
                'confirmed',
            ],
            'phone_number' => [
                'nullable',
                'string',
                'min:10',
                'max:50',
                'regex:/^\+?[0-9\s\-]{10,50}$/'
            ],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Name',
            'email' => 'E-mail',
            'password' => 'Password',
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'required' => 'the :attribute should not be empty, please add the :attribute!',
    //         'max' => 'the :attribute is too long should be at most :max!',
    //         'unique' => ':attribute already exists please try another one',
    //         'confirmed' => 'Password does not match please try again',
    //     ];
    // }

    /**
     * if the validation failed it return a json response
     */
    protected function failedValidation(Validator $validator)
    {
        Logger::failedValidation(
            'registration-validation-check',
            'Failed Validation',
            [
                'inputs' => $this->except(['password', 'password_confirmation'])
            ]
        );

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Failed Validate Data',
            'errors' => $validator->errors(),
        ], 422));
    }
}
