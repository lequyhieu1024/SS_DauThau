<?php

namespace App\Repositories;

use App\Models\ProcurementCategory;

class ProcurementCategoryRepository extends BaseRepository
{
    public function getModel()
    {
        return ProcurementCategory::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function countProcurementCategory()
    {
        return [
            'total_procurement_category' =>$this->model->count(),
            'total_active_procurement_category' =>$this->model->where('is_active', 1)->count(),
            'total_inactive_procurement_category' =>$this->model->where('is_active', 0)->count(),
        ];
    }
}
