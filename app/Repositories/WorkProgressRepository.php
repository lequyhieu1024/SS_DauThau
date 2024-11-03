<?php

namespace App\Repositories;
use App\Models\WorkProgress;

class WorkProgressRepository extends BaseRepository {
    public function getModel()
    {
        return WorkProgress::class;
    }
}
