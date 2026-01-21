<?php

namespace Modules\Assessments\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PriorityRuleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'issue_type_id' => $this->issue_type_id,
            'min_score' => $this->min_score,
            'max_score' => $this->max_score,
            'priority' => $this->priority,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
