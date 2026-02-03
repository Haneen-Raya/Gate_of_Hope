<?php

namespace Modules\HumanResources\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SpecialistResource
 * * Data transformer for the Specialist model.
 * It encapsulates the specialist profile and resolves relational dependencies
 * like IssueCategory into a clean, hierarchical JSON structure.
 * * @mixin \Modules\HumanResources\Models\Specialist
 */
class SpecialistResource extends JsonResource
{
    /**
     * Transform the specialist resource into a structured array.
     * * Key Features:
     * - Localization: Translates gender keys using language files.
     * - Relationship Mapping: Inlines essential IssueCategory data.
     * * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'gender'         => __('genders.' . $this->gender),
            'date_of_birth'  => $this->date_of_birth,
            'issue_category' => [
                'id'   => $this->issueCategory->id,
                'name' => $this->issueCategory->name,
            ],
            'user_id'        => $this->user_id,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
