<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TaskCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($tasks) {
                return [
                    'id' => $tasks->id,
                    'employees' =>$tasks->employees->map(function ($employee) {
                        return [
                            'id' => $employee->id,
                            'name' => $employee->name,
                        ];
                    }),
                    'code' => $tasks->code,
                    'name' => $tasks->name,
                    'description' => $tasks->description,
                    'difficulty_level' => $tasks->difficulty_level,
                    'project' => [
                        'id' => $tasks->project->id,
                        'name' => $tasks->project->name,
                    ],
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
