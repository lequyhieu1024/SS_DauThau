<?php

namespace App\Http\Resources\ActivityLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
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
            'id' => $this->id,
            'log_name' => $this->log_name,
            'event' => $eventTranslations[$this->event] ?? $this->event,
            'action_performer' => [
                'id' => $this->causer ? $this->causer->id : null,
                'name' => $this->causer ? $this->causer->name : null,
            ],
            'description' => $this->description,
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'properties' => $this->properties,
            'ip_address' => $this->ip_address,
            'path' => $this->path,
            'user_agent' => $this->user_agent,
            'created_at' => $this->created_at,
        ];
    }
}
