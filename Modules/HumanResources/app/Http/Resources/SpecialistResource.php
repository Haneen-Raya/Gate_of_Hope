<?php

namespace Modules\HumanResources\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'gender' => __('genders.' . $this->gender),
            'date_of_birth' => $this->date_of_birth,
            'issue_category' => [
                'id' => $this->issueCategory->id,
                'name' => $this->issueCategory->name, 
            ],
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
