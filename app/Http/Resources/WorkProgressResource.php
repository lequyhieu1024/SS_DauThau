<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project' => $this->biddingResult->project,
            'enterprise' => $this->biddingResult->enterprise,
            'name' => $this->name,
            'progress' => $this->progress,
            'expense' => $this->expense,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'feedback' => $this->feedback,
            'description' => $this->description,
            'task' => $this->taskProgresses
        ];
    }
}
