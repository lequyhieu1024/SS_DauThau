<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BidBondCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($bidBond) {
                return [
                    'id' => $bidBond->id,
                    'project_id' => $bidBond->project->name,
                    'enterprise_id' => $bidBond->enterprise->representative,
                    'bond_number' => $bidBond->bond_number,
                    'bond_amount' => $bidBond->bond_amount,
                    'bond_amount_in_words' => $bidBond->bond_amount_in_words,
                    'bond_type' => $bidBond->bond_type,
                    'issue_date' => $bidBond->issue_date,
                    'expiry_date' => $bidBond->expiry_date,
                    'description' => $bidBond->description,
                    'created_at' => $bidBond->created_at,
                    'updated_at' => $bidBond->updated_at,
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
