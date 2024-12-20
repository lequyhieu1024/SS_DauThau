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
                    'id' => $enterprise->id,
                    'user_id' => $enterprise->user_id,
                    'industry_id' => $enterprise->industries->where('is_active', true)->map(function ($industry) {
                        return $industry;
                    })->values()->toArray(),
                    'name' => $enterprise->user->name,
                    'email' => $enterprise->user->email,
                    'taxcode' => $enterprise->user->taxcode,
                    'account_ban_at' => $enterprise->user->account_ban_at,
                    'representative' => $enterprise->representative,
                    'avatar' => env('APP_URL') . '/' . $enterprise->avatar,
                    'phone' => $enterprise->phone,
                    'address' => $enterprise->address,
                    'website' => $enterprise->website,
                    'description' => $enterprise->description,
                    'establish_date' => $enterprise->establish_date,
                    'avg_document_rating' => $enterprise->avg_document_rating,
                    'registration_date' => $enterprise->registration_date,
                    'registration_number' => $enterprise->registration_number,
                    'organization_type' => $enterprise->organization_type,
                    'reputation' => $enterprise->reputation,
                    'evaluates' => EvaluateResource::collection($enterprise->evaluates),
                    'is_active' => $enterprise->is_active,
                    'is_blacklist' => $enterprise->is_blacklist,
                    'roles' => $enterprise->user->roles
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
