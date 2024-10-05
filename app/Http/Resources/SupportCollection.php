<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SupportCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($support) {
                return [
                    'id' => $support->id,
                    'user_id' => $support->user_id,
                    'title' => $support->title,
                    'email' => $support->email,
                    'phone' => $support->phone,
                    'content' => $support->content,
                    'type' => $support->type,
                    'status' => $support->status,
                    'created_at' => $support->created_at,
                ];
            }),
        ];
    }
}
