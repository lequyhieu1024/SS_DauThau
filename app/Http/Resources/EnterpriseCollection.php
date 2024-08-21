<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EnterpriseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($enterprise) {
                return [
                    'id_enterprise' => $enterprise->id,
                    'id_user' => $enterprise->user_id,
                    'representative' => $enterprise->representative,
                    'avatar' => $enterprise->avatar,
                    'phone' => $enterprise->phone,
                    'address' => $enterprise->address,
                    'website' => $enterprise->website,
                    'description' => $enterprise->description,
                    'establish_date' => $enterprise->establish_date,
                    'avg_document_rating' => $enterprise->avg_document_rating,
                    'registration_date' => $enterprise->registration_date,
                    'registration_number' => $enterprise->registration_number,
                    'organization_type' => $enterprise->organization_type,
                    'is_active' => $enterprise->is_active,
                    'is_blacklist' => $enterprise->is_blacklist,
                    'created_at' => $enterprise->created_at,
                    'updated_at' => $enterprise->updated_at,
                    'delete_at' => $enterprise->delete_at
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
