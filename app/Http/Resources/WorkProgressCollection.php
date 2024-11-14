<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WorkProgressCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($workProgress) {
                return [
                    'id' => $workProgress->id,
                    'project' => $workProgress->biddingResult->project->name,
                    'enterprise' => $workProgress->biddingResult->enterprise->user->name,
                    'name' => $workProgress->name,
                    'progress' => $workProgress->progress,
                    'expense' => $workProgress->expense,
                    'start_date' => $workProgress->start_date,
                    'end_date' => $workProgress->end_date,
                    'feedback' => $workProgress->feedback,
                    'description' => $workProgress->description,
                    'task' => $workProgress->taskProgresses,
                ];
            }),
            'total_elements' => $this->total(),
            'total_pages' => $this->lastPage(),
            'page_size' => $this->perPage(),
            'number_of_elements' => $this->count(),
            'current_page' => $this->currentPage(),
        ];
    }
}
