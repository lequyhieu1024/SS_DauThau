<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCatalogCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($postCatalog) {
                return [
                    'id' => $postCatalog->id,
                    'name' => $postCatalog->name,
                    'description' => $postCatalog->description,
                    'is_active' => $postCatalog->is_active,
                    'created_at' => $postCatalog->created_at,
                    'updated_at' => $postCatalog->updated_at,
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
