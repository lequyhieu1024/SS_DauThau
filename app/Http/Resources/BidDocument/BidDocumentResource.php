<?php

namespace App\Http\Resources\BidDocument;

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
            'project' => $this->project ? [
                'id' => $this->project->id,
                'name' => $this->project->name,
            ] : null,
            'enterprise' => $this->enterprise ? [
                'id' => $this->enterprise->id,
                'name' => $this->enterprise->representative,
            ] : null,
            'bid_bond_id' => $this->bid_bond_id,
            'submission_date' => $this->submission_date,
            'bid_price' => $this->bid_price,
            'implementation_time' => $this->implementation_time,
            'validity_period' => $this->validity_period,
            'status' => $this->status,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}