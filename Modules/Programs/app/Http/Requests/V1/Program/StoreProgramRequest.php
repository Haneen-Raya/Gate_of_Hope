<?php
namespace Modules\Programs\Http\Requests\V1\Program;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Modules\Programs\Enums\Program\ProgramStatus;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class StoreProgramRequest
 * @package Modules\Programs\Http\Requests
 */
class StoreProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Based on Spatie permission: 'programs.create'
     */
    public function authorize(): bool
    {
        return $this->user()->can('programs.create');
    }

    /**
     * Get the validation rules that apply to the request.
     * * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'issue_category_id' => 'required|exists:issue_categories,id',
            'name'              => 'required|string|max:255|unique:programs,name',
            'description'       => 'nullable|string',
            'objectives'        => 'required|array',
            'target_groups'     => 'required|string',
            'start_date'        => 'required|date|after_or_equal:today',
            'end_date'          => 'required|date|after:start_date',
            'budget'            => 'required|numeric|min:0',
            'status'            => ['required', Rule::in(values: ProgramStatus::all())],
        ];
    }
    protected function failedValidation(Validator $validator)
{
    throw new HttpResponseException(response()->json([
        'status'  => 'error',
        'message' => 'Validation Errors',
        'errors'  => $validator->errors(),
    ], 422));
}
}
