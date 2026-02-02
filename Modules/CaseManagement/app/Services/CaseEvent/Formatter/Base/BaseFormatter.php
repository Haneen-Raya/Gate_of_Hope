<?php

namespace Modules\CaseManagement\Services\CaseEvent\Formatter\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\CaseManagement\Enums\V1\CaseEventTag;

/**
 * Class BaseFormatter
 * * An architectural blueprint for transforming Eloquent Model lifecycle events into 
 * standardized Timeline/Audit records. This abstract class enforces a consistent 
 * data structure across the Case Management ecosystem.
 * * @package Modules\CaseManagement\Services\CaseEvent\Formatter\Base
 * @author Your Professional Identity / System Architect
 * @version 1.1.0
 */
abstract class BaseFormatter
{
    /**
     * Create a new formatter instance.
     * * @param Model $model The Eloquent model instance being observed.
     * @param string $action The lifecycle event (created, updated, deleted).
     */
    public function __construct(
        protected Model $model,
        protected string $action
    ) {}

    /**
     * Predicate logic to determine if the current event warrants a timeline entry.
     * * Implement this to filter out noise, such as minor updates that don't
     * impact the beneficiary's journey.
     * * @return bool
     */
    abstract public function shouldRecord(): bool;

    /**
     * Define the unique identifier for the event classification.
     * * @example 'case.status_changed', 'referral.initiated'
     * @return CaseEventTag
     */
    abstract public function eventType(): CaseEventTag;

    /**
     * Resolve the precise timestamp of the event occurrence.
     * * Defaults to system time but can be overridden to use model-specific 
     * timestamps (e.g., closed_at).
     * * @return \DateTimeInterface
     */
    abstract public function occurredAt(): \DateTimeInterface;

    /**
     * Construct the contextual metadata payload for the event.
     * * Should typically include "old" vs "new" state mapping for audit transparency.
     * * @return array<string, mixed>
     */
    abstract public function payload(): array;

    /**
     * Serialize the formatter state into a standard persistence-ready array.
     * * Utilizes intelligent polymorphic resolution to bind events to both 
     * the specific Beneficiary and their parent Case File.
     * * @return array{
     * beneficiary_id: int,
     * beneficiary_case_id: int,
     * subject_type: string,
     * subject_id: int,
     * event_tag: string,
     * payload: array,
     * actor_id: int|null,
     * occurred_at: \DateTimeInterface
     * }
     */
    public function toArray(): array
    {
        return [
            'beneficiary_id'      => $this->model->beneficiaryCase->beneficiary_id ?? $this->model->beneficiary_id,
            'beneficiary_case_id' => $this->model instanceof \Modules\CaseManagement\Models\BeneficiaryCase
                ? $this->model->id
                : $this->model->beneficiary_case_id,

            'subject_type' => $this->model::class,
            'subject_id'   => $this->model->getKey(),

            'event_tag'   => $this->eventType()->value,
            'payload'      => $this->payload(),

            'actor_id'     => Auth::id(),
            'occurred_at'  => $this->occurredAt(),
        ];
    }

    /* -----------------------------------------------------------------
     | Helper Methods: Internal State Evaluation
     |-----------------------------------------------------------------*/

    /**
     * Check if the transaction represents a new record insertion.
     * * @return bool
     */
    protected function isCreated(): bool
    {
        return $this->action === 'created';
    }

    /**
     * Determine if a specific model attribute has undergone state transition.
     * * @param string $field The column name to inspect.
     * @return bool
     */
    protected function wasChanged(string $field): bool
    {
        return $this->model->wasChanged($field);
    }

    /**
     * Check if a field was modified to match a specific target value.
     * * @param string $field The column name to inspect.
     * @param mixed $value The target value for comparison.
     * @return bool
     */
    protected function changedTo(string $field, mixed $value): bool
    {
        return $this->wasChanged($field)
            && $this->model->{$field} === $value;
    }
}
