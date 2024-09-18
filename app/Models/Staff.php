<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasRoles;
    use LogsActivity;
    use ActivityLogOptionsTrait;

    protected $table = 'staffs';
    protected $fillable = [
        'user_id',
        'avatar',
        'birthday',
        'phone',
        'gender'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    protected function getModelName(): string
    {
        return 'Nhân viên - Staff';
    }

    protected function getLogAttributes(): array
    {
        return [
            'user_id',
            'avatar',
            'birthday',
            'phone',
            'gender',
            'role_id'
        ];
    }

    public function getStaffNameByUserId($userId)
    {
        $user = $this->user()->where('id', $userId)->first();
        return $user ? $user->name : '';
    }

    protected function getFieldName(): string
    {
        return $this->getStaffNameByUserId($this->user_id);
    }
}
