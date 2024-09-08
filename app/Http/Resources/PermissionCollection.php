<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($permissions) {
                $permissions->name = __(convertText($permissions->name));
                $permissions->section = __(convertText($permissions->section));
                return [
                    'id' => $permissions->id,
                    'name' => $permissions->name,
                    'section' => $permissions->section,
                    'guard_name' => $permissions->guard_name,
                    'created_at' => $permissions->created_at,
                    'updated_at' => $permissions->updated_at,
                ];
            }),
            // 'total_elements' => $this->total(),
            // 'total_pages' => $this->lastPage(),
            // 'page_size' => $this->perPage(),
            'number_of_elements' => $this->count(),
            // 'current_page' => $this->currentPage(),
        ];
    }
}
