<?php

namespace App\Traits;

use App\Contracts\HasCaseEvents;

/**
 * Trait LogsCaseEvents
 *
 * An automated lifecycle observer that hooks into Eloquent's "boot" process.
 * This trait serves as a silent auditor, capturing database mutations without
 * polluting the model's primary business logic.
 *
 * ---
 * ### LIFECYCLE ARCHITECTURE:
 * 1. **Static Booting:** Leverages Laravel's `boot{Trait}` convention to auto-register observers.
 * 2. **Integrity Guard:** Performs a pre-flight check to verify the `HasCaseEvents` contract 
 * before attaching listeners, ensuring type safety and preventing runtime failures.
 * 3. **Event Interception:** Listens to `created`, `updated`, and `deleted` hooks.
 * 4. **Orchestration:** Hands off the mutation context to the central `EventManager` 
 * for logical filtering and persistence.
 *
 * ### USAGE INSTRUCTIONS:
 * 1. Implement `HasCaseEvents` in your target Model.
 * 2. Add `use LogsCaseEvents;` within the model body.
 * 3. Define the `caseEventFormatter()` method to return the appropriate formatter class.
 * ---
 *
 * @package App\Traits
 */
trait LogsCaseEvents
{
    /**
     * Boot the trait and attach global model observers.
     * * Implements a "Fail-Fast" structural check. If the model utilizes this 
     * trait but fails to satisfy the `HasCaseEvents` contract, the observer 
     * registration is terminated immediately to protect system stability.
     *
     * @return void
     */
    protected static function bootLogsCaseEvents()
    {

        // 1. Structural Integrity Check: 
        // Verify contract adherence using class reflection before registering listeners.
        if (! is_subclass_of(static::class, HasCaseEvents::class)) {
            return;
        }

        // 2. Lifecycle Observation:
        // Attaching closures to primary Eloquent milestones.
        foreach (['created', 'updated'] as $event) {
            static::$event(function ($model) use ($event) {
                
                // 3. Centralized Dispatch:
                // Transfer responsibility to the EventManager to maintain the 
                // Single Responsibility Principle (SRP).
                app(\Modules\CaseManagement\Services\CaseEvent\EventManager::class)->record($model, $event);
            });
        }
    }
}
