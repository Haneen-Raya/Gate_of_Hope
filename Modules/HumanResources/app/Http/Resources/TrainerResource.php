<?php

namespace Modules\HumanResources\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class TrainerResource
 * * Specialized transformer for Trainer profiles.
 * Orchestrates multiple Enums and relations to provide a comprehensive
 * representation of a trainer's professional and personal profile.
 * * @mixin \Modules\HumanResources\Models\Trainer
 */
class TrainerResource extends JsonResource
{
    /**
     * Transform the trainer resource into a localized array.
     * * Architectural Highlights:
     * - Nested Objects: Groups User and Profession data for clarity.
     * - Enum Translation: Converts raw Enum strings into readable UI labels
     * based on the active application locale.
     * - Null Safety: Uses null-safe operators for optional relations like profession.
     * * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            // Core Identity (Eager Loading recommended for performance)
            'user' => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ],

            'profession' => [
                'id'   => $this->profession->id ?? null,
                'name' => $this->profession->name ?? null,
            ],

            'bio' => $this->bio,

            // Localized Professional Attributes
            'gender'              => __('genders.' . $this->gender),
            'status'              => __('trainer_status.' . $this->status),
            'certification_level' => __('certification_level.' . $this->certification_level),

            'hourly_rate' => $this->hourly_rate,
            'is_external' => (bool) $this->is_external,
            'approved_at' => $this->approved_at,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
