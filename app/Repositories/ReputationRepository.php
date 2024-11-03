<?php

namespace App\Repositories;

use App\Models\Reputation;

class ReputationRepository extends BaseRepository {
    public function getModel()
    {
        return Reputation::class;
    }
    public function filter($data) {
        $query = $this->model->whereHas('Enterprise');

        if (isset($data['enterprise'])) {
            $query->where('enterprise_id', $data['enterprise']);
        }

        if (isset($data['score_from'])) {
            $query->where('prestige_score', '>=', $data['score_from']);
        }
        if (isset($data['score_to'])) {
            $query->where('prestige_score', '<=', $data['score_to']);
        }

        return $query->paginate($data['size'] ?? 10);
    }
}
