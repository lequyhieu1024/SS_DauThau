<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_staff' => $this->id,
            'id_user' => $this->user_id,
            'id_role' => $this->role_id,
            'role_name' => $this->role_name,
//            'code_staff' => $this->code,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'email' => $this->email,
            'phone' => $this->phone,
//            'total_bought' => $this->total_bought,
            'type' => $this->type,
            'account_ban_at' => $this->account_ban_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
