<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Evaluate extends Model
{
    use HasFactory;
    use LogsActivity;
    use ActivityLogOptionsTrait;
    protected $table = 'evaluates';

    protected $fillable = [
        'project_id',
        'enterprise_id',
        'title',
        'score',
        'evaluate',
    ];
    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function enterprise() {
        return $this->belongsTo(Enterprise::class);
    }

    protected function getModelName(): string
    {
        return 'Đánh giá kết quả - Evaluate';
    }

    protected function getLogAttributes(): array
    {
        return $this->fillable;
    }

    protected function getFieldName(): string
    {
        return $this->title;
    }
}
