<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

use function PHPSTORM_META\map;

class ProjectCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($projects) {
                return [
                    'id' => $projects->id,
                    'name' => $projects->name,
                    'tenderer' => $projects->tenderer->user->name,
                    'investor' => $projects->investor->user->name,
                    'amount' => $projects->amount,
                    'total_amount' => $projects->total_amount,
                    'status' => $projects->status,
                    'upload_time' => $projects->created_at,
                    'children' => $projects->children->map(function ($child) {
                        return [
                            'parent_id' => $child->parent_id,
                            'id' => $child->id,
                            'name' => $child->name,
                            'tenderer' => $child->tenderer->user->name,
                            'investor' => $child->investor->user->name,
                            'amount' => $child->amount,
                            'status' => $child->status,
                            'upload_time' => $child->created_at,
                        ];
                    }),
                ];
            }),
            'total_elements' => $this->total(),
            'total_pages' => $this->lastPage(),
            'page_size' => $this->perPage(),
            'number_of_elements' => $this->count(),
            'current_page' => $this->currentPage(),
        ];
    }

    //
    public function getProjectCountByIndustry()
    {
        return [
            'data' => $this->collection->map(function ($industry) {
                return [
                    'industry_name' => $industry->name,
                    'project_count' => $industry->projects_count,
                ];
            }),
        ];
    }
}
