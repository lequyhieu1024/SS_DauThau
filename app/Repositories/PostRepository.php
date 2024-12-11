<?php

namespace App\Repositories;

use App\Models\Post;


class PostRepository extends BaseRepository
{
    public function getModel()
    {
        return Post::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['short_title'])) {
            $query->where('short_title', 'like', '%' . $data['short_title'] . '%');
        }
        if (isset($data['catalog'])) {
            $query->whereHas('postCatalogs', function ($q) use ($data) {
                $q->where('post_catalogs.id', $data['catalog']);
            });
        }

        if (isset($data['author'])) {
            $query->whereHas('staff', function ($q) use ($data) {
                $q->where('staffs.id', $data['author']);
            });
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function syncPostCatalog(array $data, $id)
    {
        $post = $this->model->findOrFail($id);
        return $post->postCatalogs()->sync($data['post_catalog_id']);
    }

    public function countPost()
    {
        return $this->model->count();
    }
}
