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
        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }
        if (isset($data['status'])) {
            $data['status'] = array_filter($data['status']);
            $query->whereIn('status', $data['status']);
        }
        if (isset($data['upload_time_start'])) {
            $query->whereDate('created_at', '>=', $data['upload_time_start']);
        }

        if (isset($data['upload_time_end'])) {
            $query->whereDate('created_at', '<=', $data['upload_time_end']);
        }

        if (isset($data['investor'])) {
            $query->whereHas('investor', function ($q) use ($data) {
                $q->where('id', $data['investor']);
            });
        }

        if (isset($data['tenderer'])) {
            $query->whereHas('tenderer', function ($q) use ($data) {
                $q->where('id', $data['tenderer']);
            });
        }

        return $query->paginate(10 ?? $data['size']);
    }

    public function syncIndustry(array $data, $id)
    {
        $project = $this->model->findOrFail($id);
        return $project->industries()->sync($data['industry_id']);
    }

    public function syncProcurement(array $data, $id)
    {
        $project = $this->model->findOrFail($id);
        return $project->procurementCategories()->sync($data['procurement_id']);
    }
}
