<?php

namespace App\Models;

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
}
