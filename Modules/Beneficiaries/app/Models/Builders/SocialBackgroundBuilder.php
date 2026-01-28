<?php

namespace Modules\CaseManagement\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class SocialBackgroundBuilder
 *
 * Custom query builder responsible for applying
 * dynamic filters and search conditions on the
 * SocialBackground model.
 *
 * This builder provides a fluent interface to filter
 * social background records based on multiple parameters such as:
 *
 * - Beneficiary
 * - Education level
 * - Employment status
 * - Housing type and tenure
 * - Income level and living standard
 * - Family stability
 * - Family size range
 *
 * @package Modules\CaseManagement\Models\Builders
 *
 * @method self filterBeneficiary(?int $beneficiaryId)
 * @method self filterEducationLevel(?int $educationLevelId)
 * @method self filterEmploymentStatus(?int $employmentStatusId)
 * @method self filterHousingType(?int $housingTypeId)
 * @method self filterHousingTenure(?string $tenure)
 * @method self filterIncomeLevel(?string $incomeLevel)
 * @method self filterLivingStandard(?string $livingStandard)
 * @method self filterFamilyStability(?string $familyStability)
 * @method self filterSize(?int $min, ?int $max)
 * @method self filter(array $filters)
 */
class SocialBackgroundBuilder extends Builder
{
    /**
     * Filter social backgrounds by beneficiary ID.
     *
     * Applies filtering using the foreign key beneficiary_id.
     *
     * @param int|null $beneficiaryId
     *
     * @return self
     */
    public function filterBeneficiary(?int $beneficiaryId): self
    {
        return $this->when($beneficiaryId, fn($q) => $q->where('beneficiary_id', $beneficiaryId));
    }

    /**
     * Filter social backgrounds by education level ID.
     *
     * Applies filtering using the foreign key education_level_id.
     *
     * @param int|null $educationLevelId
     *
     * @return self
     */
    public function filterEducationLevel(?int $educationLevelId) :self
    {
        return $this->when($educationLevelId, fn($q) => $q->where('education_level_id',$educationLevelId));
    }

    /**
     * Filter social backgrounds by employment status ID.
     *
     * Applies filtering using the foreign key employment_status_id.
     *
     * @param int|null $employmentStatusId
     *
     * @return self
     */
    public function filterEmploymentStatus(?int $employmentStatusId) :self
    {
        return $this->when($employmentStatusId, fn($q) => $q->where('employment_status_id',$employmentStatusId));
    }

    /**
     * Filter social backgrounds by housing type ID.
     *
     * Applies filtering using the foreign key housing_type_id.
     *
     * @param int|null $housingTypeId
     *
     * @return self
     */
    public function filterHousingType(?int $housingTypeId) :self
    {
        return $this->when($housingTypeId, fn($q) => $q->where('housing_type_id',$housingTypeId));
    }

    /**
     * Filter social backgrounds by housing tenure.
     *
     * This filter matches the housing_tenure column exactly.
     *
     * @param string|null $tenure
     *
     * @return self
     */
    public function filterHousingTenure(?string $tenure): self
    {
        return $this->when($tenure, fn($q) => $q->where('housing_tenure',$tenure));

    }

    /**
     * Filter social backgrounds by income level.
     *
     * This filter matches the income_level column exactly.
     *
     * @param string|null $incomeLevel
     *
     * @return self
     */
    public function filterIncomeLevel(?string $incomeLevel): self
    {
        return $this->when($incomeLevel, fn($q) => $q->where('income_level',$incomeLevel));
    }

    /**
     * Filter social backgrounds by living standard.
     *
     * This filter matches the living_standard column exactly.
     *
     * @param string|null $livingStandard
     *
     * @return self
     */
    public function filterLivingStandard(?string $livingStandard): self
    {
        return $this->when($livingStandard, fn($q) => $q->where('living_standard',$livingStandard));
    }

    /**
     * Filter social backgrounds by family stability.
     *
     * This filter matches the family_stability column exactly.
     *
     * @param string|null $familyStability
     *
     * @return self
     */
    public function filterFamilyStability(?string $familyStability): self
    {
        return $this->when($familyStability, fn($q) => $q->where('family_stability',$familyStability));
    }

    /**
     * Filter social backgrounds by family size range.
     *
     * Applies minimum and maximum social background family size constraints.
     *
     * @param int|null $min
     * @param int|null $max
     *
     * @return self
     */
    public function filterSize(?int $min,?int $max ) : self
    {
        return $this
        ->when($min, fn($q) => $q->where('family_size', '>=', $min))
        ->when($max, fn($q) => $q->where('family_size', '<=', $max));
    }

    /**
     * Entry point to apply dynamic filters.
     *
     * This method provides a clean, fluent interface
     * to conditionally apply multiple social background filters.
     *
     * Supported filters:
     * - beneficiary_id         : int|null
     * - education_level_id     : int|null
     * - employment_status_id   : int|null
     * - housing_type_id        : int|null
     * - housing_tenure         : string|null
     * - income_level           : string|null
     * - living_standard        : string|null
     * - family_stability       : string|null
     * - min_family_size        : int|null
     * - max_family_size        : int|null
     *
     * @param array<string, mixed> $filters
     *
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this
            ->filterBeneficiary($filters['beneficiary_id'] ?? null)
            ->filterEducationLevel($filters['education_level_id'] ?? null)
            ->filterEmploymentStatus($filters['employment_status_id'] ?? null)
            ->filterHousingType($filters['housing_type_id'] ?? null)
            ->filterHousingTenure($filters['housing_tenure'] ?? null)
            ->filterIncomeLevel($filters['income_level'] ?? null)
            ->filterLivingStandard($filters['living_standard'] ?? null)
            ->filterFamilyStability($filters['family_stability'] ?? null)
            ->filterSize(
                $filters['min_family_size'] ?? null,
                $filters['max_family_size'] ?? null
            );
    }
}
