<?php
namespace Modules\Assessments\Http\Requests\V1\Assessment;

use Illuminate\Foundation\Http\FormRequest;

    class ImportAssessmentRequest extends FormRequest{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file'          => 'required|mimes:xlsx,csv|max:10240',
            'issue_type_id' => 'required|exists:issue_types,id'
        ];
    }

    }
?>
