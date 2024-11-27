<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'author' => new StaffResource($this->staff),
            'post_catalog_id' => $this->postCatalogs->map(function ($postCatalog) {
                return $postCatalog->id;
            })->values()->toArray(),
            'post_catalog_name' => $this->postCatalogs->map(function ($postCatalog) {
                return $postCatalog->name;
            })->values()->toArray(),
            'short_title' => $this->short_title,
            'title' => $this->title,
            'content' => $this->content,
            'thumbnail' => $this->thumbnail,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
