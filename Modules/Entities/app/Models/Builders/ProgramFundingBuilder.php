<?php

namespace Modules\Entities\Models\Builders;

use App\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProgramFundingBuilder
 *
 * Custom query builder responsible for applying
 * dynamic filters and search conditions on the ProgramFunding model.
 *
 * This builder provides a fluent interface to filter program fundings
 * based on request parameters such as:
 *
 * - program ID
 * - donor entity ID
 * - funding date range
 * - funding amount range
 * - currency
 *
 * @package Modules\Entities\Models\Builders
 *
 * @method self filterProgram(?int $programId)
 * @method self filterDonorEntity(?int $donorEntityId)
 * @method self filterProgramFundingDate(?string $start, ?string $end)
 * @method self filterAmount(?int $min, ?int $max)
 * @method self filterCurrency(?string $currency)
 * @method self filter(array $filters)
 */
class ProgramFundingBuilder extends Builder
{
    /**
     * Filter program fundings by program ID.
     *
     * Applies filtering using the foreign key program_id.
     *
     * @param int|null $programId
     *
     * @return self
     */
    public function filterProgram(?int $programId): self
    {
        return $this->when($programId, fn($q) => $q->where('program_id', $programId));
    }

    /**
     * Filter program fundings by donor entity ID.
     *
     * Applies filtering using the foreign key donor_entity_id.
     *
     * @param int|null $donorEntityId
     *
     * @return self
     */
    public function filterDonorEntity(?int $donorEntityId): self
    {
        return $this->when($donorEntityId, fn($q) => $q->where('donor_entity_id', $donorEntityId));

    }

    /**
     * Filter program fundings by funding date range.
     *
     * Filters fundings between a start and end date.
     *
     * @param string|null $start
     *      Start date (inclusive).
     *
     * @param string|null $end
     *      End date (inclusive).
     *
     * @return self
     */
    public function filterProgramFundingDate(?string $start,?string $end ) : self
    {
        return $this
        ->when($start, fn($q) => $q->whereDate('start_date', '>=', $start))
        ->when($end, fn($q) => $q->whereDate('end_date', '<=', $end));
    }

    /**
     * Filter program fundings by amount range.
     *
     * Applies minimum and maximum funding amount constraints.
     *
     * @param int|null $min
     * @param int|null $max
     *
     * @return self
     */
    public function filterAmount(?int $min,?int $max ) : self
    {
        return $this
        ->when($min, fn($q) => $q->where('amount', '>=', $min))
        ->when($max, fn($q) => $q->where('amount', '<=', $max));
    }

    /**
     * Filter program fundings by currency.
     *
     * This filter matches the currency column exactly.
     *
     * @param string|null $currency
     *
     * @return self
     */
    public function filterCurrency(?string $currency): self
    {
        return $this->when($currency, fn($q) => $q->where('currency', $currency));

    }

    /**
     * Apply dynamic filters on program fundings.
     *
     * This is the main entry point for applying multiple filters
     * based on request parameters.
     *
     * Supported filters:
     * - program_id       : int|null
     * - donor_entity_id  : int|null
     * - start_date       : string|null
     * - end_date         : string|null
     * - min_amount       : int|null
     * - max_amount       : int|null
     * - currency         : string|null
     *
     * @param array<string, mixed> $filters
     *
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this
            ->filterProgram($filters['program_id'] ?? null)
            ->filterDonorEntity($filters['donor_entity_id'] ?? null)
            ->filterProgramFundingDate($filters['start_date'] ?? null,$filters['end_date'] ?? null)
            ->filterAmount($filters['min_amount'] ?? null,$filters['max_amount'] ?? null)
            ->filterCurrency($filters['currency'] ?? null);
    }
}
