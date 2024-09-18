<?php

namespace App\Repositories;

use App\Models\Project;

class ProjectRepository extends BaseRepository
{

    public function getModel()
    {
        return Project::class;
    }

    public function filter($data)
    {
        $query = $this->model->with('children')->whereNull('parent_id');
        // logic loc du an
        return $query->paginate(10 ?? $data['size']);
    }
    public function syncIndustry(array $data, $id)
    {
        $project = $this->model->findOrFail($id);
        return $project->industries()->sync($data['industry_id']);
    }
}
