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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'industry_id' => $this->industries->where('is_active', true)->map(function ($industry) {
                return $industry->id;
            })->values()->toArray(),
            'name' => $this->user->name,
            'email' => $this->user->email,
            'taxcode' => $this->user->taxcode,
            'account_ban_at' => $this->user->account_ban_at,
            'representative' => $this->representative,
            'avatar' => env('APP_URL') . '/' . $this->avatar,
            'phone' => $this->phone,
            'address' => $this->address,
            'website' => $this->website,
            'description' => $this->description,
            'establish_date' => $this->establish_date,
            'avg_document_rating' => $this->avg_document_rating,
            'registration_date' => $this->registration_date,
            'registration_number' => $this->registration_number,
            'organization_type' => $this->organization_type,
            'reputation' => $this->reputation->prestige_score,
            'is_active' => $this->is_active,
            'is_blacklist' => $this->is_blacklist,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
