<?php

namespace App\Repositories;

use App\Models\Enterprise;

class EnterpriseRepository extends BaseRepository
{
    public function getModel()
    {
        return Enterprise::class;
    }
    public function filter($data)
    {
        $query = $this->model->query();
        if (isset($data['status'])) {
            $query->whereHas('user', function ($query) use ($data) {
                $query->where('account_ban_at', $data['account_ban_at']);
            });
        }
        if (isset($data['name'])) {
            $query->whereHas('user', function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['name'] . '%');
            });
        }
        if (isset($data['avg_document_rating_from'])) {
            $query->where('avg_document_rating', '>=', $data['avg_document_rating_from']);
        }
        if (isset($data['avg_document_rating_to'])) {
            $query->where('avg_document_rating', '<=', $data['avg_document_rating_to']);
        }
        if (isset($data['organization_type'])) {
            $query->where('organization_type', '=', $data['organization_type']);
        }
        if (isset($data['is_active'])) {
            $query->where('is_active', '=', $data['is_active']);
        }
        if (isset($data['is_blacklist'])) {
            $query->where('is_blacklist', '=', $data['is_blacklist']);
        }
        if (isset($data['industry_ids']) && is_array($data['industry_ids'])) {
            $data['industry_ids'] = array_filter($data['industry_ids'], function ($value) {
                return $value !== null && $value !== '';
            });

            if (!empty($data['industry_ids'])) {
                $query->whereHas('industries', function ($query) use ($data) {
                    $query->whereIn('industry_id', $data['industry_ids']);
                });
            }
        }
        return $query->paginate($data['size'] ?? 10);
    }

    public function syncIndustry(array $data, $id)
    {
        $enterprise = $this->model->findOrFail($id);
        return $enterprise->industries()->sync($data['industry_id']);
    }
}
