<?php

namespace Modules\HumanResources\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProfessionBuilder
 * * Dedicated query engine for professional taxonomies.
 * Facilitates fuzzy searching and system-code mapping for job roles.
 */
class ProfessionBuilder extends Builder
{
    /**
     * Perform a fuzzy partial match search on the profession name.
     */
    public function byName(string $name): self
    {
        return $this->where('name', 'like', "%{$name}%");
    }

    /**
     * Match by the unique organizational system code.
     */
    public function byCode(string $code): self
    {
        return $this->where('code', $code);
    }

    /**
     * Dynamic Filter Orchestrator.
     * * Processes an array of filters to build a complex query fluently.
     * * @param array<string, mixed> $filters Contains 'name', 'code', etc.
     */
    public function filter(array $filters): self
    {
        return $this
            ->when($filters['name'] ?? null, fn($q, $name) => $q->byName($name))
            ->when($filters['code'] ?? null, fn($q, $code) => $q->byCode($code))
            ->latest();
    }
}
