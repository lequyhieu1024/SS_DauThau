<?php

namespace App\Repositories;

use App\Models\BidBond;

class BidBondRepository extends BaseRepository
{
    public function getModel()
    {
        return BidBond::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['bond_number'])) {
            $query->where('bond_number', 'like', '%' . $data['bond_number'] . '%');
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function getBondNumberAndIds()
    {
        return $this->model->select('id','bond_number')->get();
    }

    public function countBidBond()
    {
        return $this->model->count();
    }
}
