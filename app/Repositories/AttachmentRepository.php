<?php

namespace App\Repositories;

use App\Models\Attachment;

class AttachmentRepository extends BaseRepository
{
    public function getModel()
    {
        return Attachment::class;
    }
}
