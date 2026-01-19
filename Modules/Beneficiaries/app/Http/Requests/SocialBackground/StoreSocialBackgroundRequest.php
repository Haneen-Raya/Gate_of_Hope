<?php

namespace Modules\Beneficiaries\Http\Requests\SocialBackground;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Beneficiaries\Enums\FamilyStability;
use Modules\Beneficiaries\Enums\HousingTenure;
use Modules\Beneficiaries\Enums\IncomeLevel;
use Modules\Beneficiaries\Enums\LivingStandard;

class StoreSocialBackgroundRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'beneficiary_id'        => ['required','integer','exists:beneficiaries,id','unique:social_backgrounds,beneficiary_id'],
            'education_level_id'    => ['required', 'integer', 'exists:education_levels,id'],
            'employment_status_id'  => ['required', 'integer', 'exists:employment_statuses,id'],
            'housing_type_id'       => ['required', 'integer', 'exists:housing_types,id'],
            'housing_tenure'        => ['required','string', Rule::in(HousingTenure::all())],
            'income_level'          => ['required','string',Rule::in(IncomeLevel::all())],
            'living_standard'       => ['required','string',Rule::in(LivingStandard::all())],
            'family_size    '       => ['required', 'integer', 'min:1','max:20'],
            'family_stability'      => ['required','string',Rule::in(FamilyStability::all())],
        ];
    }
}
