<?php

namespace App\Repositories;

use App\Models\Banner;

class BannerRepository extends BaseRepository
{
    public function getModel()
    {
        return Banner::class;
    }
    public function filter($data)
    {
        $query = $this->model->query();
        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function deleteBanner($id)
    {
        $banner = $this->model->find($id);
        if ($banner) {
            $banner->delete();
        }
        return $banner;
    }
}
