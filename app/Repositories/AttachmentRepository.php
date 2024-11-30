<?php

namespace App\Repositories;

use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;

class AttachmentRepository extends BaseRepository
{
    public function getModel()
    {
        return Attachment::class;
    }

    public function filter($data)
    {
        $query = $this->model->query();
        if (isset($data['project'])) {
            $query->where('project_id', $data['project']);
        }
        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }
        if (isset($data['type'])) {
            $query->where('type', $data['type']);
        }
        return $query->orderBy('id', 'DESC')->paginate($data['size'] ?? 10);
    }

    public function createAttachment($files, $projectId, $userId, $projectName)
    {

        $attachments = [];

        foreach ($files as $file) {
            $newFilename = $projectName . '.' . now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $relativePath = "uploads/documents/{$newFilename}";

            $file->move(public_path('uploads/documents'), $newFilename);

            if (!file_exists(public_path('uploads/documents/' . $newFilename))) {
                throw new \Exception('Không tìm thấy tập tin sau khi di chuyển.');
            }

            $data = [
                'user_id' => $userId,
                'project_id' => $projectId,
                'name' => $newFilename,
                'path' => $relativePath,
                'type' => $file->getClientOriginalExtension(),
                'size' => filesize(public_path('uploads/documents/' . $newFilename)),
                'is_active' => true,
            ];

            $attachments[] = new AttachmentResource($this->create($data));
        }

        return $attachments;
    }

}
