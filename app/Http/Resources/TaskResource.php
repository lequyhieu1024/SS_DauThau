<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'employees' =>$this->employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                ];
            }),
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'difficulty_level' => $this->difficulty_level,
        ];
    }
}
