<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FundingSourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($fundingSource) {
                return [
                    'id' => $fundingSource->id,
                    'name' => $fundingSource->name,
                    'description' => $fundingSource->description,
                    'code' => $fundingSource->code,
                    'type' => $fundingSource->type,
                    'is_active' => $fundingSource->is_active,
                    'created_at' => $fundingSource->created_at,
                    'updated_at' => $fundingSource->updated_at,
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
