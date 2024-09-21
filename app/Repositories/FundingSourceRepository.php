<?php

namespace App\Repositories;

class FundingSourceRepository extends BaseRepository
{

    public function getModel()
    {
        return \App\Models\FundingSource::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }

        if (isset($data['code'])) {
            $query->where('code', $data['code']);
        }

        if (isset($data['type'])) {
            $query->where('type', $data['type']);
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function createFundingSource(array $data)
    {
        return $this->model::create($data);
    }

    public function findFundingSourceById($id)
    {
        return $this->model->find($id);
    }

    public function updateFundingSource(array $data, $id)
    {
        $fundingSource = $this->model->find($id);
        if ($fundingSource) {
            $fundingSource->update($data);
        }
        return $fundingSource;
    }

    public function deleteFundingSource($id)
    {
        $fundingSource = $this->model->find($id);
        if ($fundingSource) {
            $fundingSource->delete();
        }
        return $fundingSource;
    }
}
