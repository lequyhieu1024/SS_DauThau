<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionAnswerCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($questionAnswer){
                return [
                    'id' => $questionAnswer->id,
                    'project_id' => $questionAnswer->project_id,
                    'question_content' => $questionAnswer->question_content,
                    'answer_content' => $questionAnswer->answer_content,
                    'asked_by' => $questionAnswer->asked_by,
                    'answered_by' => $questionAnswer->answered_by,
                    'status' => $questionAnswer->status,
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
