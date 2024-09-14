<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Staff;
use App\Traits\ActivityLogOptionsTrait;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity, ActivityLogOptionsTrait;

    /**
     * Lấy giá trị xác định sẽ được lưu trong subject claim của JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Trả về một mảng key-value, chứa các custom claims sẽ được thêm vào JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'taxcode',
        'account_ban_at',
    ];
    protected $guard_name = 'api';


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }
    public function staff()
    {
        return $this->hasOne(Staff::class);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
            ->wherePivot('model_type', self::class);
    }

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    protected function getModelName(): string
    {
        return 'Người dùng - User';
    }

    protected function getLogAttributes(): array
    {
        return [
            'name',
            'email',
            'taxcode',
            'account_ban_at',
        ];
    }

    protected function getFieldName(): string
    {
        return $this->name;
    }

    public function complained(){
        return $this->hasMany(FeedbackComplaint::class, 'complaint_by');
    }
    
    public function responded(){
        return $this->hasMany(FeedbackComplaint::class,'responded_by');
    }
}
