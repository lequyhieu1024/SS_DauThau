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
                return $industry;
            })->values()->toArray(),
            'attachments' => $this->attachments,
            'project_investor' => $this->projectInvestors->map(function ($projectInvestor) {
                return [
                    'id' => $projectInvestor->id,
                    'name' => $projectInvestor->name,
                ];
            }),
            'project_tenderer' => $this->projectTenderers->map(function ($projectTenderer) {
                return [
                    'id' => $projectTenderer->id,
                    'name' => $projectTenderer->name,
                ];
            }),
            'project_win' => BiddingResultResource::collection($this->biddingResults),
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
            'reputation' => new ReputationResource($this->reputation),
            'evaluates' => EvaluateResource::collection($this->evaluates),
            'is_active' => $this->is_active,
            'is_blacklist' => $this->is_blacklist,
            'roles' => $this->user->roles
        ];
    }
}
