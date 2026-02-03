<?php

namespace Modules\Programs\Http\Requests\V1\ActivitySession;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Programs\Enums\V1\ActivitySessionStatus;
use Modules\Programs\Models\ActivitySession;

/**
 * Class StoreActivitySessionRequest
 *
 * Handles validation and preparation of data when creating a new Activity Session.
 *
 * Responsibilities:
 * - Validates required fields for creating a session
 * - Ensures proper date and time formats
 * - Checks trainer availability conflicts
 * - Normalizes certain fields (e.g., status)
 */
class StoreActivitySessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Actual permissions are handled via ActivitySessionPolicy.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for creating a new Activity Session.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'activity_id' => [
                'required',
                'exists:activities,id',
            ],
            'trainer_id' => [
                'required',
                'exists:trainers,id',
            ],
            'session_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],
            'capacity' => [
                'required',
                'integer',
                'min:1',
                'max:500',
            ],
            'status' => [
                Rule::enum(ActivitySessionStatus::class),
            ],
            'location' => [
                'required',
                'array',
            ],
            'location.lat' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'location.lng' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
            'session_notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    /**
     * Add additional validation after the main rules are applied.
     *
     * Checks if the trainer already has another session during the requested time.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if (! $this->trainer_id || ! $this->session_date) {
                return;
            }

            $hasConflict = ActivitySession::query()
                ->forTrainerSessions($this->trainer_id)
                ->whereDate('session_date', $this->session_date)
                ->whereIn('status', [
                    ActivitySessionStatus::SCHEDULED,
                    ActivitySessionStatus::ONGOING,
                ])
                ->where(function ($q) {
                    $q->whereBetween('start_time', [$this->start_time, $this->end_time])
                      ->orWhereBetween('end_time', [$this->start_time, $this->end_time])
                      ->orWhere(function ($q) {
                          $q->where('start_time', '<=', $this->start_time)
                            ->where('end_time', '>=', $this->end_time);
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
     * Prepare data before validation.
     *
     * Normalizes the status field to lowercase if provided.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('status')) {
            $this->merge([
                'status' => strtolower($this->status),
            ]);
        }
    }
}
