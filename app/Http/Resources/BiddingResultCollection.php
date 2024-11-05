<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BiddingResultCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($biddingResult) {
                return [
                    'id' => $biddingResult->id,
                    'enterprise' => $biddingResult->enterprise,
                    'project' => $biddingResult->project,
                    'bid_document' => $biddingResult->biddingDocument,
                    'win_amount' => $biddingResult->win_amount,
                    'decision_number' => $biddingResult->decision_number,
                    'decision_date' => $biddingResult->decision_date,
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
