<?php

namespace Modules\Beneficiaries\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Custom Query Builder for the Beneficiary Model.
 *
 * This class handles all database search and filter logic for beneficiaries.
 * By decoupling this logic from the Controller, we make the code reusable
 * and easier to test.
 *
 * @extends Builder<\Modules\Beneficiaries\Models\Beneficiary>
 */
class BeneficiaryBuilder extends Builder
{

    /**
     * Scope to search beneficiary by system_code (Exact match).
     *
     * @param string $systemCode The unique system-generated code.
     * @return self
     */
    public function searchSystemCode(string $systemCode): self
    {
        return $this->where('system_code', "{$systemCode}");
    }

    /**
     * Scope to search beneficiary by national_id hash.
     * 
     * Note: The input national_id is hashed using SHA256 to match the stored 'identity_hash'.
     *
     * @param string $nationalId The plain-text national ID.
     * @return self
     */
    public function searchNationalId(string $nationalId): self
    {
        $nationalId = hash('sha256', $nationalId);

        return $this->where('identity_hash', "{$nationalId}");
    }

    /**
     * Filter beneficiaries by their resident governorate.
     *
     * @param string $governorate The name or code of the governorate.
     * @return self
     */
    public function byGovernorate(string $governorate): self
    {
        return $this->where('governorate', $governorate);
    }

    /**
     * Filter beneficiaries by gender.
     *
     * @param string $gender The gender value (e.g., male, female).
     * @return self
     */
    public function byGender(string $gender): self
    {
        return $this->where('gender', $gender);
    }

    /**
     * Filter beneficiaries by a specific disability type.
     *
     * @param string $type The disability classification.
     * @return self
     */
    public function byDisability(string $type): self
    {
        return $this->where('disability_type', $type);
    }

    /**
     * Filter beneficiaries by their residence type.
     *
     * @param string $type The residence classification (e.g., owned, rented, camp).
     * @return self
     */
    public function byResidenceType(string $type): self
    {
        return $this->where('residence_type', $type);
    }

    /**
     * Filter beneficiaries based on their identity verification status.
     *
     * @param bool $status True for verified, false for pending.
     * @return self
     */
    public function verified(bool $status = true): self
    {
        return $this->where('is_verified', $status);
    }

    /**
     * Filter beneficiaries based on their displacement status.
     *
     * @param bool $status True if the beneficiary is displaced.
     * @return self
     */
    public function displaced(bool $status = true): self
    {
        return $this->where('is_displaced', $status);
    }

    /**
     * Filter beneficiaries within a specific birth date range.
     * * This method conditionally applies start and end date boundaries
     * to accurately filter beneficiaries by age groups or birth periods.
     *
     * @param string|null $from The start date (YYYY-MM-DD).
     * @param string|null $to The end date (YYYY-MM-DD).
     * @return self
     */
    public function bornBetween(?string $from, ?string $to): self
    {
        return $this->when($from, fn($q) => $q->whereDate('date_of_birth', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date_of_birth', '<=', $to));
    }



    /**
     * Apply dynamic filters from an HTTP request array.
     *
     * This method provides a clean, fluent interface to conditionally apply
     * various search parameters from a user request.
     *
     * @param array<string, mixed> $filters Associative array containing filter keys.
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this

            // ---------------------------------------------------
            // 1. Universal Search (System Code or National ID)
            // ---------------------------------------------------
            // We use a nested where group to ensure OR logic doesn't interfere with other filters.
            // It leverages existing scopes for consistent hashing and query logic.
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->searchSystemCode((string) $search)
                        ->orWhere(fn($inner) => $inner->searchNationalId((string) $search));
                });
            })

            // ---------------------------------------------------
            // 2. Demographic & Location Scopes
            // ---------------------------------------------------
            // Applies specialized scopes for categorical data filtering.
            ->when($filters['governorate'] ?? null, fn($q, $val) => $q->byGovernorate((string) $val))
            ->when($filters['gender'] ?? null, fn($q, $val) => $q->byGender((string) $val))
            ->when($filters['disability_type'] ?? null, fn($q, $val) => $q->byDisability((string) $val))
            ->when($filters['residence_type'] ?? null, fn($q, $val) => $q->byResidenceType((string) $val))

            // ---------------------------------------------------
            // 3. Boolean Status Toggles
            // ---------------------------------------------------
            // Using isset() to correctly handle boolean false (0) values.
            ->when(isset($filters['is_verified']), fn($q) => $q->verified((bool) $filters['is_verified']))
            ->when(isset($filters['is_displaced']), fn($q) => $q->displaced((bool) $filters['is_displaced']))

            // ---------------------------------------------------
            // 4. Date Period Scope
            // ---------------------------------------------------
            // Encapsulates date range logic into a single atomic scope call.
            ->bornBetween(
                $filters['birth_date_from'] ?? null,
                $filters['birth_date_to'] ?? null
            );
    }
}
