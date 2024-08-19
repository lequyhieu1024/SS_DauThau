<?php

namespace App\Repositories;

class BiddingFieldRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\BiddingField::class;
    }

    public function filter($data)
    {

    }

}
