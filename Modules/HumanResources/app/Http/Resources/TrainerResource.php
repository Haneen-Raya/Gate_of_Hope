<?php

namespace Modules\HumanResources\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\HumanResources\Enums\Gender;
use Modules\HumanResources\Enums\TrainerStatus;
use Modules\HumanResources\Enums\CertificationLevel;

class TrainerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            // User info (assuming relation loaded)
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],

            'profession' => [
                'id' => $this->profession->id ?? null,
                'name' => $this->profession->name ?? null,
            ],

            'bio' => $this->bio, 

            // Enums translated
            'gender' => __('genders.' . $this->gender),
            'status' => __('trainer_status.' . $this->status),
            'certification_level' => __('certification_level.' . $this->certification_level),

            'hourly_rate' => $this->hourly_rate,
            'is_external' => $this->is_external,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
