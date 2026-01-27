<?php

namespace Modules\Core\Models\Builders;

use App\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Builder;
use MatanYadaev\EloquentSpatial\Objects\Point;

/**
 * Class RegionBuilder
 * * Provides custom query logic for the Region model, including spatial searches,
 * text-based filtering, and global scope management.
 * * @package Modules\Core\Models\Builders
 */
class RegionBuilder extends Builder
{
    /**
     * Entry point for applying dynamic filters from the request.
     * * It coordinates between spatial filtering and global scope management
     * based on the provided filter array.
     * * @param array<string, mixed> $filters Contains keys: 'is_active', 'lat', 'lng', 'distance'.
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this
            ->handleGlobalScopeBypass($filters['is_active'] ?? null)
            ->whereDistanceNearby(
                $filters['lat'] ?? null,
                $filters['lng'] ?? null,
                $filters['distance'] ?? null
            );
    }

    /**
     * Search regions by name or code.
     * * Performs a partial match on the name using 'like' and an exact match
     * on the uppercase version of the region code.
     * * @param string|null $term The search keyword (name or code).
     * @return self
     */
    public function search(?string $term): self
    {
        return $this->when($term, function ($q) use ($term) {
            $q->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('code', strtoupper($term));
            });
        });
    }

    /**
     * Core spatial filtering method.
     * * 1. Calculates the distance between the user's coordinates and the region's location.
     * 2. Adds a virtual 'distance' column to the result set.
     * 3. Optionally filters results within a specific radius (distance).
     * 4. Orders results by proximity (nearest first).
     * * @param float|null $lat Latitude of the reference point.
     * @param float|null $lng Longitude of the reference point.
     * @param float|null $distance Maximum radius in meters for filtering.
     * @return self
     */
    public function whereDistanceNearby(?float $lat, ?float $lng, ?float $distance): self
    {
        if ($lat && $lng) {
            $point = new Point($lat, $lng);

            // Injects 'distance' alias into the SQL query
            $this->withDistance('location', $point, 'distance');

            // Apply radius filter if provided
            if ($distance) {
                $this->whereDistance('location', $point, '<', $distance);
            }

            // Sort from nearest to farthest
            $this->orderByDistance('location', $point, 'asc');
        }
        return $this;
    }

    /**
     * Manages ActiveScope based on the desired 'is_active' state.
     * * If the user explicitly requests inactive records (is_active = false),
     * it removes the global ActiveScope and filters for inactive entries only.
     * * @param mixed $isActiveValue The status flag from the request.
     * @return self
     */
    protected function handleGlobalScopeBypass($isActiveValue): self
    {
        if (isset($isActiveValue) && ! (bool) $isActiveValue) {

            return $this->withoutGlobalScope(ActiveScope::class)
                        ->where('is_active', false);
        }

        return $this;
    }
}
