<?php

namespace App\Traits;

use App\Models\Scopes\ActiveScope;


/**
 * Trait HasActiveState
 * * Automatically attaches the ActiveScope to the model.
 */
trait HasActiveState
{
    /**
     * Boot the active state trait for the model.
     * * @return void
     */
    public static function bootHasActiveState(): void
    {
        static::addGlobalScope(new ActiveScope);
    }
}
