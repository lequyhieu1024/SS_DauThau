<?php

namespace App\Repositories;

use App\Models\PostCatalog;

class PostCatalogRepository extends BaseRepository
{
    public function getModel()
    {
        return PostCatalog::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }
    public function getPostsByCatalogLandipage($id)
    {
        $catalog = $this->model->findOrFail($id);
        return $catalog->posts()->paginate(10);
    }

    public function countPostCatalog()
    {
        return $this->model->count();
    }
}
