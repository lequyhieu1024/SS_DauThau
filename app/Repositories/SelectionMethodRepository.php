<?php

namespace App\Repositories;

class SelectionMethodRepository extends BaseRepository
{

    public function getModel()
    {
        return \App\Models\SelectionMethod::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['method_name'])) {
            $query->where('method_name', 'like', '%' . $data['method_name'] . '%');
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function createSelectionMethod(array $data)
    {
        return $this->model::create($data);
    }

    public function findSelectionMethodById($id)
    {
        return $this->model->find($id);
    }

    public function updateSelectionMethod(array $data, $id)
    {
        $selectionMethod = $this->model->find($id);
        if ($selectionMethod) {
            $selectionMethod->update($data);
        }
        return $selectionMethod;
    }

    public function deleteSelectionMethod($id)
    {
        $selectionMethod = $this->model->find($id);
        if ($selectionMethod) {
            $selectionMethod->delete();
        }
        return $selectionMethod;
    }
    public function getSelectionMethod()
    {
        return $this->model->select('id', 'method_name')->get();
    }
}
