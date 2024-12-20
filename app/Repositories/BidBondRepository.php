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

        if (isset($data['bond_type'])) {
            $query->where('bond_type', $data['bond_type']);
        }

        if (isset($data['project_id'])) {
            $query->where('project_id', $data['project_id']);
        }

        if (isset($data['enterprise_id'])) {
            $query->where('enterprise_id', $data['enterprise_id']);
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function getBondNumberAndIds()
    {
        return $this->model->select('id','bond_number')->orderBy('id', 'desc')->get();
    }

    public function countBidBond()
    {
        return [
            'total_bid_bonds' =>$this->model->count(),
            'total_not_expired_bid_bonds' =>$this->model->where('expiry_date', '>=' , now())->count(),
            'total_expired_bid_bonds' =>$this->model->where('expiry_date', '<' , now())->count(),
        ];
    }
}
