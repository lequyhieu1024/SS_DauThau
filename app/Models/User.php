<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Staff;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

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
     * Mặc định hash password bằng Hash, đây là Accessor, có thể tìm hiểu Accessor và Mulator trên docs Laravel nhé.
     * Với những trường cần Hash thì chỉ cần $request->data thay vì Hash::make($request->data)
     */

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }


    /**
     * Chỗ này để viết relationship
     */

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function enterprise()
    {
        return $this->hasOne(Enterprise::class);
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
    public function getAllPermissions($user_id, $type)
    {
        $role_id = "";
        if ($type == 'staff') {
            $role_id = Staff::where('user_id', $user_id)->pluck('role_id')->first();
        }
        $permissions = DB::table('role_has_permissions')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $role_id)
            ->select('permissions.name')
            ->pluck('permissions.name')
            ->toArray();
        return $permissions;
    }
}
