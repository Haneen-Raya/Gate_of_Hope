<?php

namespace Modules\CaseManagement\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CaseManagement\Enums\SessionType;

class CaseSessionResource extends JsonResource
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
            'beneficiary_case_id' => $this->beneficiary_case_id,

            // Session type translated via enum
            'session_type' => $this->session_type ? SessionType::from($this->session_type)->label() : null,

            'session_date' => $this->session_date,
            'duration_minutes' => $this->duration_minutes,

            // Notes and recommendations translated based on current locale
            'notes' => $this->notes,
            'recommendations' => $this->recommendations,

            'conducted_by' => [
                'id' => $this->conducted_by,
                'name' => $this->whenLoaded('specialist', fn() => $this->specialist->user->name ?? null),
            ],

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
