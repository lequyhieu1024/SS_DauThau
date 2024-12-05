<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EvaluateCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($evaluate) {
                return [
                    'id' => $evaluate->id,
                    'project' => $evaluate->project,
                    'enterprise' => $evaluate->enterprise,
                    'user' => $evaluate->enterprise->user,
                    'title' => $evaluate->title,
                    'score' => $evaluate->score,
                    'evaluate' => $evaluate->evaluate,
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
