<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\ActivityLogOptionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

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

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    public function enterprise()
    {
        return $this->hasOne(Enterprise::class);
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
}
