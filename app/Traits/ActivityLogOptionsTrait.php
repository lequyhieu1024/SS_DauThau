<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Request;

trait ActivityLogOptionsTrait
{
    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->ip_address = request()->ip();
        $activity->user_agent = Request::header('User-Agent');
        $activity->path = Request::path();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getLogAttributes())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->getModelName())
            ->setDescriptionForEvent(function (string $eventName) {
                $eventDescriptions = [
                    'created' => 'đã được tạo mới',
                    'updated' => 'đã được cập nhật',
                    'deleted' => 'đã bị xóa'
                ];

                $nameField = $this->getFieldName();
                $description = $this->getModelName().": \"".$nameField."\" ".($eventDescriptions[$eventName] ?? $eventName).':';
                if ($eventName === 'updated') {
                    $dirtyAttributes = $this->getDirty();
                    unset($dirtyAttributes['updated_at']);
                    if (isset($dirtyAttributes['name'])) {
                        $oldName = $this->getOriginal('name');
                        $description = $this->getModelName().": \"".$oldName."\" đã được cập nhật:";
                    }
                    foreach ($dirtyAttributes as $attribute => $newValue) {
                        $oldValue = $this->getOriginal($attribute);
                        if (is_bool($newValue) || is_bool($oldValue)) {
                            $newValue = $newValue ? 'true' : 'false';
                            $oldValue = $oldValue ? 'true' : 'false';
                        }
                        $description .= ".\n- $attribute được thay đổi từ \"$oldValue\" thành \"$newValue\"";
                    }
                }
                return $description;
            });
    }

    abstract protected function getModelName(): string;

    abstract protected function getLogAttributes(): array;

    abstract protected function getFieldName(): string;
}
