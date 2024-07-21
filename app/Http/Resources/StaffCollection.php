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
            'total_elements' => $this->total(),
            'total_pages' => $this->lastPage(),
            'page_size' => $this->perPage(),
            'number_of_elements' => $this->count(),
            'current_page' => $this->currentPage(),
        ];
    }
}
