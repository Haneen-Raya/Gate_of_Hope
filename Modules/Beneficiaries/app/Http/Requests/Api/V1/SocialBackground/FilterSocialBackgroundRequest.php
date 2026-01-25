<?php

namespace Modules\Beneficiaries\Http\Requests\Api\V1\SocialBackground;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Beneficiaries\Enums\V1\FamilyStability;
use Modules\Beneficiaries\Enums\V1\HousingTenure;
use Modules\Beneficiaries\Enums\V1\IncomeLevel;
use Modules\Beneficiaries\Enums\V1\LivingStandard;

class FilterSocialBackgroundRequest extends FormRequest
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
            'education_level_id'    => ['sometimes', 'integer', 'exists:education_levels,id'],
            'employment_status_id'  => ['sometimes', 'integer', 'exists:employment_statuses,id'],
            'housing_type_id'       => ['sometimes', 'integer', 'exists:housing_types,id'],
            'housing_tenure'        => ['sometimes', 'string', Rule::in(HousingTenure::all())],
            'income_level'          => ['sometimes', 'string', Rule::in(IncomeLevel::all())],
            'living_standard'       => ['sometimes', 'string', Rule::in(LivingStandard::all())],
            'family_size_min'       => ['sometimes', 'integer', 'min:1'],
            'family_size_max'       => ['sometimes', 'integer', 'min:1'],
            'family_stability'      => ['sometimes', 'string', Rule::in(FamilyStability::all())],
            'per_page'              => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page'                  => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
