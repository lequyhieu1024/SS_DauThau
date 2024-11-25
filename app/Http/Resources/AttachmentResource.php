<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user,
            'project' => $this->project,
            'name' => $this->name,
            'url' => url("/documents/{$this->name}"),
            'type' => $this->type,
            'size' => $this->size,
        ];
    }
}
