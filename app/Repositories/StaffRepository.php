<?php

namespace App\Repositories;

use Carbon\Carbon;

class StaffRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\Staff::class;
    }

    public function filter($data)
    {
        $query = $this->model->with('user');
        if (isset($data['status'])) {
            $query->whereHas('user', function ($query) use ($data) {
                $query->where('account_ban_at', $data['account_ban_at']);
            });
        }
        if (isset($data['age_from'])) {
            $dateFrom = Carbon::now()->subYears($data['age_from'])->startOfDay()->toDateString();
            $query->where('birthday', '<=', $dateFrom);
        }

        if (isset($data['age_to'])) {
            $dateTo = Carbon::now()->subYears($data['age_to'])->endOfDay()->toDateString();;
            $query->where('birthday', '>=', $dateTo);
        }
        if (isset($data['name'])) {
            $query->whereHas('user', function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['name'] . '%');
            });
        }
        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function showStaff($id)
    {
        return $this->model->with('user')->find($id);
    }

    public function countStaff()
    {
        return $this->model->count();
    }
}
