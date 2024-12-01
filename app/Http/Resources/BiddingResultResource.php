<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BiddingResultResource extends JsonResource
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
            'user' => $this->enterprise->user,
            'enterprise' => $this->enterprise,
            'project' => $this->project,
            'bid_document' => $this->biddingDocument,
            'win_amount' => $this->win_amount,
            'decision_number' => $this->decision_number,
            'decision_date' => $this->decision_date,
        ];
    }
}
