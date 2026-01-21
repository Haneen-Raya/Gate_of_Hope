<?php
namespace Modules\Assessments\Http\Resources\V1\GoogleForm;

use Illuminate\Http\Resources\Json\JsonResource;

class GoogleFormResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'issue_type' => [
                'id' => $this->issueType?->id,
                'name' => $this->issueType?->name,
                'label' => $this->issueType?->label,
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
