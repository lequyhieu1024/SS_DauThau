<?php

namespace App\Repositories;

use App\Models\BidDocument;

class BidDocumentRepository extends BaseRepository
{
    public function getModel()
    {
        return BidDocument::class;
    }
}
