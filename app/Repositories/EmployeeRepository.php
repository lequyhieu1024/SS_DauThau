<?php

namespace App\Repositories;

use App\Models\Employee;
use Carbon\Carbon;

class EmployeeRepository extends BaseRepository
{
    public function getModel()
    {
        return Employee::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();
        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }
        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }
        if (isset($data['code'])) {
            $query->where('code', 'like', '%' . $data['code'] . '%');
        }
        if (isset($data['education_level'])) {
            $query->where('education_level', $data['education_level']);
        }
        if (isset($data['enterprise'])) {
            $query->where('enterprise_id', $data['enterprise']);
        }
        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }
        if (isset($data['age_from'])) {
            $dateFrom = Carbon::now()->subYears($data['age_from'])->startOfDay()->toDateString();
            $query->where('birthday', '<=', $dateFrom);
        }
        if (isset($data['age_to'])) {
            $dateTo = Carbon::now()->subYears($data['age_to'])->endOfDay()->toDateString();;
            $query->where('birthday', '>=', $dateTo);
        }
        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function getNameAndIds()
    {
        return $this->model->select('id', 'name')->where('status','doing')->orderBy('id', 'DESC')->get();
    }
}
