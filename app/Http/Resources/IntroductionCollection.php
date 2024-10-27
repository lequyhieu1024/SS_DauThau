<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IntroductionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function($introduction){
                return [
                    'id' =>$introduction->id,
                    'introduction' => $introduction->introduction,
                    'is_use' => $introduction->is_use,
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
