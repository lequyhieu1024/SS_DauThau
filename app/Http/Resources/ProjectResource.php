<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'funding_source' => $this->fundingSource,
            'tenderer' => new EnterpriseResource($this->tenderer),
            'investor' => new EnterpriseResource($this->investor),
            'staff' => new StaffResource($this->staff),
            'selection_method' => $this->selectionMethod,
            'industries' => $this->industries->where('is_active', true)->map(function ($industry) {
                return [
                    'id' => $industry->id,
                    'name' => $industry->name,
                ];
            })->values()->toArray(),
            'procurement_categories' => $this->procurementCategories->where('is_active', true)->map(function ($industry) {
                return [
                    'id' => $industry->id,
                    'name' => $industry->name,
                ];
            })->values()->toArray(),
            'attachments' => $this->attachments->map(function ($attachment) {
                return [
                    'type' => $attachment->type,
                    'path' => env('APP_URL') . '/' . $attachment->path,
                    'name' => $attachment->name,
                ];
            })->values()->toArray(),
            'children' => $this->children,
            'evaluation_criterias' => $this->evaluationCriterias,
            'decision_number_issued' => $this->decision_number_issued,
            'name' => $this->name,
            'is_domestic' => $this->is_domestic,
            'location' => $this->location,
            'amount' => $this->amount,
            'total_amount' => $this->total_amount,
            'description' => $this->description,
            'submistion_method' => $this->submistion_method,
            'receiving_place' => $this->receiving_place,
            'bid_submission_start' => $this->bid_submission_start,
            'bid_submission_end' => $this->bid_submission_end,
            'bid_opening_date' => $this->bid_opening_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'approve_at' => $this->approve_at,
            'decision_number_approve' => $this->decision_number_approve,
            'status' => $this->status,
        ];
    }
}
