<?php

namespace Modules\Programs\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Programs\Enums\ActivitySessionStatus;

class ActivitySessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            // Activity info (relation)
            'activity' => [
                'id' => $this->activity->id ?? null,
                'name' => $this->activity->name ?? null,
            ],

            // Trainer info (relation)
            'trainer' => [
                'id' => $this->trainer->id ?? null,
                'name' => $this->trainer->user->name ?? null,
                'email' => $this->trainer->user->email ?? null,
                'gender' => __('genders.' . $this->trainer->gender),
                'certification_level' => __('certification_level.' . $this->trainer->certification_level),
            ],

            // Session details
            'session_date' => $this->session_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'location' => $this->location, 
            'capacity' => $this->capacity,

            // Translatable fields
            'session_notes' => $this->session_notes,

            // Status enum 
            'status' => __('activity_session_status.' . $this->status),

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
