<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReputationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($reputation) {
                return [
                    'id' => $reputation->id,
                    'enterprise' => $reputation->enterprise,
                    'number_of_blacklist' => $reputation->number_of_blacklist,
                    'number_of_ban' => $reputation->number_of_ban,
                    'prestige_score' => $reputation->prestige_score,
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
