<?php

namespace Modules\Programs\Services;

use Modules\Programs\Models\Program;
use Modules\Programs\Jobs\SendProgramAlertJob;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class ProgramAlertService
 * * This service handles the business logic for monitoring program health,
 * specifically regarding resource availability and management notifications.
 * It ensures that Program Managers are proactively alerted when resources
 * reach critical thresholds.
 *
 * @package Modules\Programs\Services
 */
class ProgramAlertService
{
    /**
     * Audit program resources and dispatch alerts if thresholds are breached.
     * * This method retrieves the program along with its creator (acting as the
     * Program Manager) and associated resources. It evaluates the inventory
     * level of each resource; if a resource falls below the safety threshold
     * (5 units), an asynchronous notification job is dispatched.
     *
     * @param int $programId The unique identifier of the program to be audited.
     * * @return void
     * @throws ModelNotFoundException If the program does not exist in the database.
     * * @uses \Modules\Programs\Models\Program::creator To identify the responsible manager.
     * @uses \Modules\Programs\Models\Program::programResources To evaluate inventory levels.
     * @uses \Modules\Programs\Jobs\SendProgramAlertJob::dispatch To handle mailing asynchronously.
     */
    public function checkAndNotify(int $programId): void
    {
        // Eager load relations to prevent N+1 query issues during the audit process.
        $program = Program::with(['creator', 'programResources'])->findOrFail($programId);

        /** @var \Modules\Core\Models\User|null $manager */
        $manager = $program->creator;

        // Security and Role Validation: Ensure the alert is only sent to authorized managers.
        if (!$manager || !$manager->hasRole('program_manager')) {
            return;
        }

        // Iterate through all assigned resources to check for critical shortages.
        foreach ($program->programResources as $resource) {
            /** * Safety Threshold Check
             * Threshold: < 5 units.
             * Business Rule: Resources below this level are considered critical.
             */
            if ($resource->quantity < 5) {
                SendProgramAlertJob::dispatch($manager->email, [
                    'program_id'   => $program->id,
                    'program_name' => $program->name,
                    'resource_name'=> $resource->name,
                    'quantity'     => $resource->quantity,
                    'title'        => 'Critical Resource Shortage',
                    'body'         => "This is a warning that the resource [{$resource->name}] is nearly depleted."
                ]);
            }
        }
    }
}
