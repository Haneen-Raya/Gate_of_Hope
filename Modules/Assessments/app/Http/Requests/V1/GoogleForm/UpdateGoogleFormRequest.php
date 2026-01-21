<?php
namespace Modules\Assessments\Http\Requests\V1\GoogleForm;

use Illuminate\Foundation\Http\FormRequest;

    class UpdateGoogleFormRequest extends FormRequest {
        public function authorize() {
            return true;
            }

        public function rules() {
            return [
                'url'           => 'sometimes|url',
                'issue_type_id' => 'sometimes|exists:issue_types,id|unique:google_forms,issue_type_id,' . $this->route('google_form'),
            ];
        }
}

?>
