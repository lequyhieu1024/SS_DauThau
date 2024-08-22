<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BiddingFieldCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
          'data' => $this->collection->map(function ($biddingField){
              return [
                  'id' => $biddingField->id,
                  'name' => $biddingField->name,
                  'description' => $biddingField->description,
                  'code' => $biddingField->code,
                  'is_active' => $biddingField->is_active,
                  'created_at' => $biddingField->created_at,
                  'updated_at' => $biddingField->updated_at,
                  'parent' => $biddingField->parent ? [
                      'id' => $biddingField->parent_id,
                      'name' => $biddingField->parent->name,
                  ] : null,
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
