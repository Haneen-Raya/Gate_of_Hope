<?php

namespace Modules\Programs\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Programs\Enums\ActivitySessionStatus;

/**
 * Class ActivitySessionResource
 *
 * * Key Features:
 * - Relation Flattening: Simplifies complex relationships (Activity, Trainer, User) into readable nested arrays.
 * - Localization: Automatically translates Enums and gender fields using Laravel's translation engine.
 * - Null Safety: Uses null coalescing operators to prevent errors if relations are missing.
 *
 * @package Modules\Programs\Http\Resources
 */
class ActivitySessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Maps model attributes to a structured API response.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            // Activity info (relation)
            // Provides essential activity details without exposing the full object.
            'activity' => [
                'id' => $this->activity->id ?? null,
                'name' => $this->activity->name ?? null,
            ],

            // Trainer info (relation)
            // Cascades through Trainer to User model for identity details.
            // Includes localized gender and certification level strings.
            'trainer' => [
                'id' => $this->trainer->id ?? null,
                'name' => $this->trainer->user->name ?? null,
                'email' => $this->trainer->user->email ?? null,
                'gender' => __('genders.' . $this->trainer->gender),
                'certification_level' => __('certification_level.' . $this->trainer->certification_level),
            ],

            // Session details
            // Direct mapping of temporal and spatial attributes.
            'session_date' => $this->session_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'location' => $this->location,
            'capacity' => $this->capacity,

            // Translatable fields
            'session_notes' => $this->session_notes,

            // Status enum
            // Converts the raw database status key into a human-readable, localized string.
            'status' => __('activity_session_status.' . $this->status),

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
