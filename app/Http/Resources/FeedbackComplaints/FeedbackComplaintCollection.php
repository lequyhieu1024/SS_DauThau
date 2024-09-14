<?php

namespace App\Http\Resources\FeedbackComplaints;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FeedbackComplaintCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($feedbackComplaint) {
                return [
                    'id' => $feedbackComplaint->id,
                    'project_id' => $feedbackComplaint->project_id,
                    'complaint_by' => $feedbackComplaint->complainedBy->name,
                    'responded_by' => $feedbackComplaint->respondedBy->name ?? null,
                    'content' => $feedbackComplaint->content,
                    'response_content' => $feedbackComplaint->response_content,
                    'status' => $feedbackComplaint->status,
                    'created_at' => $feedbackComplaint->created_at,
                    'updated_at' => $feedbackComplaint->updated_at,
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
