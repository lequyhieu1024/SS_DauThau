<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BidBondResource extends JsonResource
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
            'project_id' => $this->project->name,
            'enterprise_id' => $this->enterprise->representative,
            'bond_number' => $this->bond_number,
            'bond_amount' => $this->bond_amount,
            'bond_amount_in_words' => $this->bond_amount_in_words,
            'bond_type' => $this->bond_type,
            'issue_date' => $this->issue_date,
            'expiry_date' => $this->expiry_date,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
