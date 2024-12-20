<?php

namespace App\Http\Resources\BidDocument;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BidDocumentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->transform(function ($bidDocument) {
                return [
                    'id' => $bidDocument->id,
                    'project' => [
                        'id' => $bidDocument->project->id,
                        'name' => $bidDocument->project->name,
                    ],
                    'enterprise' => [
                        'id' => $bidDocument->enterprise->id,
                        'name' => $bidDocument->enterprise->user->name,
                    ],
                    'bid_price' => $bidDocument->bid_price,
                    'status' => $bidDocument->status,
                    'submission_date' => $bidDocument->submission_date,
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
