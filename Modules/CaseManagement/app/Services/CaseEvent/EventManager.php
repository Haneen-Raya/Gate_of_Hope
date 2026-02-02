<?php

namespace Modules\CaseManagement\Services\CaseEvent;

use Illuminate\Database\Eloquent\Model;
use Modules\CaseManagement\Models\CaseEvent;
use Modules\CaseManagement\Services\CaseEvent\Formatter\Base\BaseFormatter;


/**
 * Class EventManager
 *
 * The central orchestration engine responsible for capturing and persisting 
 * domain events. It acts as a bridge between Eloquent lifecycle hooks and 
 * the Case Management timeline.
 *
 * * Key Responsibilities:
 * 1. **Model Validation:** Ensures only models implementing `HasCaseEvents` are processed.
 * 2. **Dynamic Resolution:** Resolves the appropriate Formatter through Laravel's Service Container.
 * 3. **Predicate Filtering:** Respects the `shouldRecord` guard to prevent timeline pollution.
 * 4. **Persistence:** Serializes the formatted data into the `case_events` audit table.
 *
 * @package Modules\CaseManagement\Services\CaseEvent
 */
class EventManager
{
    /**
     * Orchestrate the recording of a model event.
     *
     * This method executes the full transformation pipeline:
     * Identification -> Resolution -> Evaluation -> Persistence.
     *
     * @param Model $model The source Eloquent model being observed.
     * @param string $action The lifecycle action (e.g., created, updated).
     * @return void
     */
    public function record(Model $model, string $action): void
    {
        // 1. Dynamic Formatter Resolution
        // Retrieve the formatter class string from the model and instantiate it 
        // with the current state via Dependency Injection.
        $formatterClass = $model->caseEventFormatter();

        /** @var BaseFormatter $formatter */
        $formatter = app($formatterClass, [
            'model'  => $model,
            'action' => $action,
        ]);

        // 2. Business Logic Validation
        // Exit early if the formatter determines this specific mutation 
        // is not worth recording (e.g., non-critical field update).
        if (! $formatter->shouldRecord()) {
            return;
        }

        // 3. Persistence
        // Transform the formatter object into a database-ready array 
        // and commit it to the case_events table.
        CaseEvent::create($formatter->toArray());
    }
}
