<?php

namespace Modules\Entities\Models\Builders;

use App\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class ProgramFundingBuilder extends Builder
{
    /**
     * Filter by program.
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
     * Filter by donor entity .
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
     * Filter by program funding date range.
     * @param string|null $start
     * @param string|null $end
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
     * Filter by program funding amount range.
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
     * Filter by currency .
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
     * Entry point to apply dynamic filters.
     *
     * This method provides a clean, fluent interface to conditionally apply
     * various search parameters from a user request.
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
