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
        return $this->model->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function getBannersLangipage()
    {
        return $this->model->where('is_active', true)->orderBy('id', 'desc')->get();
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
