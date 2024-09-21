<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'taxcode' => $this->user->taxcode,
            'avatar' => env('APP_URL') . '/' . $this->avatar,
            'email' => $this->user->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'account_ban_at' => $this->user->account_ban_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => $this->user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
        ];
    }
}
