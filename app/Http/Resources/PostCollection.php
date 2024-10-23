<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($post) {
                return [
                    'id' => $post->id,
                    'author_id' => $post->author_id,
                    'author_name' => $post->staff->user->name,
                    'post_catalog_id' => $post->postCatalogs->map(function ($postCatalog) {
                        return $postCatalog->id;
                    })->values()->toArray(),
                    'post_catalog_name' => $post->postCatalogs->map(function ($postCatalog) {
                        return $postCatalog->name;
                    })->values()->toArray(),
                    'short_title' => $post->short_title,
                    'title' => $post->title,
                    'content' => $post->content,
                    'thumbnail' => $post->thumbnail,
                    'status' => $post->status,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
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
