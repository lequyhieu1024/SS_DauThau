<?php

namespace App\Repositories;

use Spatie\Activitylog\Models\Activity;

class ActivityLogRepository extends BaseRepository
{
    protected $eventTranslations = [
        'Tạo mới' => 'created',
        'Cập nhật' => 'updated',
        'Xóa' => 'deleted',
    ];

    public function getModel()
    {
        return Activity::class;
    }

    protected function translateEvent($event)
    {
        return $this->eventTranslations[$event] ?? $event;
    }

    public function filter($data)
    {
        $query = $this->model->query();

        if (isset($data['log_name'])) {
            $query->where('log_name', 'like', '%' . $data['log_name'] . '%');
        }

        if (isset($data['event'])) {
            $translatedEvent = $this->translateEvent($data['event']);
            $query->where('event', 'like', '%' . $translatedEvent . '%');
        }

        if (isset($data['action_performer'])) {
            $query->whereHas('causer', function ($q) use ($data) {
                $q->where('name', 'like', '%' . $data['action_performer'] . '%');
            });
        }

        if (isset($data['description'])) {
            $query->where('description', 'like', '%' . $data['description'] . '%');
        }


        if (isset($data['start_date'])) {
            $query->whereDate('created_at', '>=', $data['start_date']);
        }

        if (isset($data['end_date'])) {
            $query->whereDate('created_at', '<=', $data['end_date']);
        }


        return $query->orderBy('id', 'desc')->paginate($data['size'] ?? 10);
    }
}
