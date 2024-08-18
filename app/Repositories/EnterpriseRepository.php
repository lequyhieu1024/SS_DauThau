<?php

namespace App\Repositories;

use App\Models\Enterprise;

class EnterpriseRepository extends BaseRepository
{
    public function getModel()
    {
        return Enterprise::class;
    }
}
