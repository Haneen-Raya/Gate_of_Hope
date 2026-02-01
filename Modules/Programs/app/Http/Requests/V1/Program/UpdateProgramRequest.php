<?php

namespace Modules\Programs\Http\Requests\V1\Program;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Programs\Enums\Program\ProgramStatus;

class UpdateProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('programs.update');
    }

    public function rules(): array
    {
        return [
            'issue_category_id' => 'sometimes|exists:issue_categories,id',
            'name'              => 'sometimes|string|max:255|unique:programs,name,' . $this->route('program'),
            'status'            => ['required', Rule::in(values: ProgramStatus::all())],
            'end_date'          => 'sometimes|date|after:start_date',
            'budget'            => 'sometimes|numeric|min:0',
        ];
    }
}
