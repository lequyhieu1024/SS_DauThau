<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StaffCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($staff) {
                return [
                    'id_staff' => $staff->staff_id,
                    'id_user' => $staff->user_id,
                    'id_role' => $staff->role_id,
                    'role_name' => $staff->role_name,
                    'code_staff' => $staff->code,
                    'name' => $staff->name,
                    'avatar' => $staff->avatar,
                    'email' => $staff->email,
                    'phone' => $staff->phone,
                    'total_bought' => $staff->total_bought,
                    'type' => $staff->type,
                    'account_ban_at' => $staff->account_ban_at,
                    // 'guard_name' => $staff->guard_name,
                    'created_at' => $staff->created_at,
                    'updated_at' => $staff->updated_at,
                ];
            }),
            'meta' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'first_page_url' => $this->url(1),
                'last_page_url' => $this->url($this->lastPage()),
                'next_page_url' => $this->nextPageUrl(),
                'prev_page_url' => $this->previousPageUrl(),
            ],
        ];
    }
}
