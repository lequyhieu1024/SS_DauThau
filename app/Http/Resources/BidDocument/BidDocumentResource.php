<?php

namespace App\Http\Resources\BidDocument;

use App\Http\Resources\EnterpriseResource;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BidDocumentResource extends JsonResource
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
            'project' => $this->project,
            'enterprise' => $this->enterprise,
            'user_enterprise' => $this->enterprise->user,
            'bid_bond' => $this->bidBond,
            'submission_date' => $this->submission_date,
            'bid_price' => $this->bid_price,
            'implementation_time' => $this->implementation_time,
            'validity_period' => $this->validity_period,
            'status' => $this->status,
            'note' => $this->note,
            'file' => $this->file,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
