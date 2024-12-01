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
