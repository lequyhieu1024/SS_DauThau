<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionAnswerResource extends JsonResource
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
            'project_id' => $this->project_id,
            'question_content' => $this->question_content,
            'answer_content' => $this->answer_content,
            'asked_by' => $this->asked_by,
            'answered_by' => $this->answered_by,
            'status' => $this->status,
        ];
    }
}
