<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnterpriseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_enterprise' => $this->id,
            'id_user' => $this->user_id,
            'representative' => $this->representative,
            'avatar' => $this->avatar,
            'phone' => $this->phone,
            'address' => $this->address,
            'website' => $this->website,
            'description' => $this->description,
            'establish_date' => $this->establish_date,
            'avg_document_rating' => $this->avg_document_rating,
            'registration_date' => $this->registration_date,
            'registration_number' => $this->registration_number,
            'organization_type' => $this->organization_type,
            'is_active' => $this->is_active,
            'is_blacklist' => $this->is_blacklist,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'delete_at' => $this->delete_at
        ];
    }
}