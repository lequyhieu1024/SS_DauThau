<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AttachmentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'user' => $attachment->user,
                    'project' => $attachment->project,
                    'name' => $attachment->name,
                    'url' => url("/documents/{$attachment->name}"),
                    'type' => $attachment->type,
                    'size' => $attachment->size,
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
