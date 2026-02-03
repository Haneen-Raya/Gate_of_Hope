<?php

namespace App\Traits;

use App\Contracts\CacheInvalidatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Trait AutoFlushCache
 *
 * * Responsibility:
 * Monitors model changes and executes cache purging logic.
 * This ensures that the Service Layer (which reads from Cache)
 * always serves the most recent database state.
 */
trait AutoFlushCache
{
    /**
     * Boot the trait and register Eloquent observers.
     * Laravel automatically calls methods following the "boot[TraitName]" convention.
     */
    public static function bootAutoFlushCache(): void
    {
        // Handle both creation and updates
        static::saved(function (Model $model) {
            self::invalidate($model);
        });

        // Handle record removal
        static::deleted(function (Model $model) {
            self::invalidate($model);
        });
    }

    /**
     * Logic to execute the invalidation.
     * * @param Model $model The instance being modified.
     */
    private static function invalidate(Model $model): void
    {
        // Type-hint check: Ensure the model knows which tags it owns.
        if (! $model instanceof CacheInvalidatable) {
            return;
        }

        // Retrieve the specific tags defined in the Model's implementation.
        $tags = $model->getCacheTagsToInvalidate();

        if (! empty($tags)) {
            // Flush only the relevant tags to keep the rest of the cache warm.
            Cache::tags($tags)->flush();
        }
    }
}
