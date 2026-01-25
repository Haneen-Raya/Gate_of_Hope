<?php

namespace Modules\Core\Observers;

use Modules\Core\Models\Region;
use Illuminate\Support\Str;

/**
 * Class RegionObserver
 * * This observer automates data handling for the Region model.
 * Specifically, it ensures every region has a unique short code upon creation.
 */
class RegionObserver
{
    /**
     * Handle the Region "creating" event.
     * * This method generates a default 3-letter code from the region's name
     * if no code was manually provided during creation.
     * * @param Region $region The region instance being created.
     * @return void
     */
    public function creating(Region $region): void
    {
        if (empty($region->code)) {
            // Logic: Take the first 3 characters of the name.
            // Note: The setCodeAttribute Mutator in the Model will
            // handle converting this to Uppercase automatically.
            $region->code = substr($region->name, 0, 3);
        }
    }
}
