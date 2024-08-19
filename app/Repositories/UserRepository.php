<?php

namespace App\Repositories;


class UserRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\User::class;
    }
}
