<?php
namespace Modules\Assessments\Http\Requests\V1\GoogleForm;

use Illuminate\Foundation\Http\FormRequest;

    class StoreGoogleFormRequest extends FormRequest{

        public function authorize() {
            return true;
        }


        public function rules(){
            return [
                'url'           => 'required|url',
                'issue_type_id' => 'required|exists:issue_types,id|unique:google_forms,issue_type_id'
            ];

        }
    }
?>
