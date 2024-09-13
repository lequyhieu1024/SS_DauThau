<?php

namespace App\Services;

use App\Models\File;

use App\Repositories\FileRepository;

class FileService
{
    protected $fileRepository;
    protected $documentMimeTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'application/rtf',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
    ];

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }


    // app/Services/FileService.php

    public function upload($file, $type, $userId)
    {
        if ($type === 'document' && !in_array($file->getMimeType(), $this->documentMimeTypes)) {
            throw new \Exception('Invalid document type');
        }

        $directory = "uploads/{$type}s";
        $timestamp = now()->timestamp;
        $extension = $file->getClientOriginalExtension();
        $uniqueId = uniqid();
        $newFilename = "{$timestamp}_{$uniqueId}.{$extension}";

        $file->move(public_path($directory), $newFilename);
        $relativePath = "{$directory}/{$newFilename}";

        $fileModel = new File();
        $fileModel->user_id = $userId;
        $fileModel->filename = $newFilename;
        $fileModel->url = url("/files/{$type}s/{$fileModel->filename}");
        $fileModel->path = $relativePath;
        $fileModel->type = $type;

        // Check if the file exists before getting its size
        if (file_exists(public_path($relativePath))) {
            $fileModel->size = filesize(public_path($relativePath));
        } else {
            throw new \Exception('File not found after upload');
        }

        return $this->fileRepository->save($fileModel);
    }

    public function findById($id)
    {
        return $this->fileRepository->findOrFail($id);
    }
}
