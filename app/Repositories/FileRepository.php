<?php

namespace App\Repositories;

use App\Models\File;

class FileRepository extends BaseRepository
{
    public function getModel()
    {
        return File::class;
    }

    public function save(File $file): File
    {
        $file->save();
        return $file;
    }
}
