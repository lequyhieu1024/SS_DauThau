<?php

namespace App\Repositories;


use App\Models\Enterprise;
use Illuminate\Support\Facades\Auth;

class UserRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\User::class;
    }
    public function getEnterpriseId ($user_id) {
        $enterprise = Enterprise::where('user_id', $user_id)->first();
        if ($enterprise == null) {
            return null;
        }
        return $enterprise->id;
    }
}
