<?php

namespace App\Http\Resources\ActivityLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ActivityLogCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $eventTranslations = [
            'created' => 'Tạo mới',
            'updated' => 'Cập nhật',
            'deleted' => 'Xóa',
        ];

        return [
            'data' => $this->collection->map(function ($activityLog) use ($eventTranslations) {
                return [
                    'id' => $activityLog->id,
                    'log_name' => $activityLog->log_name,
                    'event' => $eventTranslations[$activityLog->event] ?? $activityLog->event,
                    'action_performer' => $activityLog->causer ? $activityLog->causer->name : null,
                    'description' => $activityLog->description,
                    'created_at' => $activityLog->created_at,
                ];
            }),
            'total_elements' => $this->total(),
            'total_pages' => $this->lastPage(),
            'page_size' => $this->perPage(),
            'number_of_elements' => $this->count(),
            'current_page' => $this->currentPage(),
        ];
    }
}
