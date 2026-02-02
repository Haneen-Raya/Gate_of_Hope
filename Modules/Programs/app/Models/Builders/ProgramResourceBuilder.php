<?php

namespace Modules\Programs\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProgramResourceBuilder
 * * Provides specialized query filtering logic for ProgramResource models.
 * This class extends the base Eloquent Builder to encapsulate complex
 * filtering and search operations, promoting code reusability and cleaner models.
 * * @package Modules\Programs\Models\Builders
 */
class ProgramResourceBuilder extends Builder
{
    /**
     * Apply a set of filters to the query.
     * * This centralized method conditionally applies filters based on the
     * provided array keys, allowing for dynamic API-driven searches.
     * * @param array $filters Array containing filter criteria (name, type, program_id, min_cost)
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this->when($filters['name'] ?? null, fn($q, $name) => $q->whereNameLike($name))
                    ->when($filters['type'] ?? null, fn($q, $type) => $q->whereType($type))
                    ->when($filters['program_id'] ?? null, fn($q, $id) => $q->whereProgram($id))
                    ->when($filters['min_cost'] ?? null, fn($q, $min) => $q->whereCostAbove($min));
    }

    /**
     * Filter the query by resource name using a fuzzy search.
     * * @param string $name The partial or full name of the resource
     * @return self
     */
    public function whereNameLike(string $name): self
    {
        return $this->where('name', 'like', "%{$name}%");
    }

    /**
     * Filter the query by specific resource type.
     * * @param string $type The resource type value (e.g., educational, logistics)
     * @return self
     */
    public function whereType(string $type): self
    {
        return $this->where('resource_type', $type);
    }

    /**
     * Filter the query by its parent program ID.
     * * @param int $id The unique identifier of the program
     * @return self
     */
    public function whereProgram(int $id): self
    {
        return $this->where('program_id', $id);
    }

    /**
     * Filter the query to include only resources with a cost above a certain threshold.
     * * @param float $min The minimum cost value
     * @return self
     */
    public function whereCostAbove(float $min): self
    {
        return $this->where('cost', '>=', $min);
    }
}
