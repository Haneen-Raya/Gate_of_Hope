<?php

namespace Modules\HumanResources\Http\Requests\V1\Specialist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\HumanResources\Enums\Gender;

class StoreSpecialistRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gender' => ['required', new Enum(Gender::class)],
            'date_of_birth' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'issue_category_id' => 'required|exists:issue_categories,id',];
    }
}
