<?php

namespace Modules\Programs\Http\Requests\V1\ActivitySession;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Programs\Enums\ActivitySessionStatus;
use Modules\Programs\Models\ActivitySession;

/**
 * Class UpdateActivitySessionRequest
 *
 * Handles validation logic for updating an existing Activity Session.
 *
 * Responsibilities:
 * - Allow partial updates (PATCH-style behavior)
 * - Validate data types and relations
 * - Prevent invalid time ranges
 * - Prevent trainer schedule conflicts
 * - Block updates on completed or cancelled sessions
 *
 * Note:
 * - Authorization is handled via ActivitySessionPolicy
 * - Business logic (saving, status transitions) is handled in the Service layer
 */
class UpdateActivitySessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization logic is delegated to the Policy.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for updating an activity session.
     *
     * All fields are optional (sometimes) to allow partial updates.
     */
    public function rules(): array
    {
        return [
            // Activity relationship
            'activity_id' => [
                'sometimes',
                'exists:activities,id',
            ],

            // Trainer relationship
            'trainer_id' => [
                'sometimes',
                'exists:trainers,id',
            ],

            // Session date (cannot be in the past)
            'session_date' => [
                'sometimes',
                'date',
                'after_or_equal:today',
            ],

            // Time window
            'start_time' => [
                'sometimes',
                'date_format:H:i',
            ],

            'end_time' => [
                'sometimes',
                'date_format:H:i',
                'after:start_time',
            ],

            // Capacity constraints
            'capacity' => [
                'sometimes',
                'integer',
                'min:1',
                'max:500',
            ],

            // Session lifecycle status
            'status' => [
                'sometimes',
                Rule::enum(ActivitySessionStatus::class),
            ],

            // Location (latitude / longitude)
            'location' => [
                'sometimes',
                'array',
            ],
            'location.lat' => [
                'required_with:location',
                'numeric',
                'between:-90,90',
            ],
            'location.lng' => [
                'required_with:location',
                'numeric',
                'between:-180,180',
            ],

            // Optional notes
            'session_notes' => [
                'sometimes',
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    /**
     * Additional validation logic after basic rules pass.
     *
     * Prevents assigning a trainer to overlapping sessions
     * on the same date and time window.
     *
     * Important:
     * - Ignores the current session itself during conflict checks
     * - Uses final values (request input OR existing model values)
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            /** @var ActivitySession $session */
            $session = $this->route('activity_session');

            // Resolve final values after update (fallback to existing model)
            $trainerId = $this->trainer_id ?? $session->trainer_id;
            $date      = $this->session_date ?? $session->session_date;
            $start     = $this->start_time ?? $session->start_time;
            $end       = $this->end_time ?? $session->end_time;

            // If required values are missing, skip conflict check
            if (! $trainerId || ! $date || ! $start || ! $end) {
                return;
            }

            // Check for overlapping sessions for the same trainer
            $hasConflict = ActivitySession::query()
                ->where('id', '!=', $session->id) // Ignore current session
                ->forTrainerSessions($trainerId)
                ->whereDate('session_date', $date)
                ->whereIn('status', [
                    ActivitySessionStatus::SCHEDULED,
                    ActivitySessionStatus::ONGOING,
                ])
                ->where(function ($q) use ($start, $end) {
                    // Partial overlap at the start
                    $q->whereBetween('start_time', [$start, $end])

                        // Partial overlap at the end
                      ->orWhereBetween('end_time', [$start, $end])

                        // Existing session fully contains the new range
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('start_time', '<=', $start)
                            ->where('end_time', '>=', $end);
                      });
                })
                ->exists();

            if ($hasConflict) {
                $validator->errors()->add(
                    'trainer_id',
                    'The trainer already has another session during this time.'
                );
            }
        });
    }

    /**
     * Prepare request data before validation.
     *
     * Blocks any modification attempt on sessions
     * that are already completed or cancelled.
     */
    protected function prepareForValidation(): void
    {
        $session = $this->route('activity_session');

        if (
            $session &&
            in_array($session->status, [
                ActivitySessionStatus::COMPLETED,
                ActivitySessionStatus::CANCELLED,
            ])
        ) {
            abort(422, 'Completed or cancelled sessions cannot be modified.');
        }
    }
}
