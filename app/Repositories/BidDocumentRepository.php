<?php

namespace App\Repositories;

use App\Models\BidDocument;

class BidDocumentRepository extends BaseRepository
{
    public function getModel()
    {
        return BidDocument::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['project_id'])) {
            $query->where('project_id', $data['project_id']);
        }

        if (isset($data['enterprise_id'])) {
            $query->where('enterprise_id', $data['enterprise_id']);
        }

        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }

        if (isset($data['start_date'])) {
            $query->whereDate('submission_date', '>=', $data['start_date']);
        }

        if (isset($data['end_date'])) {
            $query->whereDate('submission_date', '<=', $data['end_date']);
        }

        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }

    public function findByProjectAndEnterprise($projectId, $enterpriseId)
    {
        return $this->model->where('project_id', $projectId)
            ->where('enterprise_id', $enterpriseId)
            ->first();
    }
}
