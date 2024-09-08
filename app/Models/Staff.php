<?php

namespace App\Models;

use App\Traits\ActivityLogOptionsTrait;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasRoles;
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

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
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
            'roles.name'
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
