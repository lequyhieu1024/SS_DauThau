<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StaffCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($staff) {
                return [
                    'id' => $staff->id,
                    'user_id' => $staff->user_id,
                    'name' => $staff->user->name,
                    'avatar' => env('APP_URL') . '/' . $staff->avatar,
                    'email' => $staff->user->email,
                    'phone' => $staff->phone,
                    'birthday' => $staff->birthday,
                    'gender' => $staff->gender,
                    'account_ban_at' => $staff->user->account_ban_at,
                    'created_at' => $staff->user->created_at,
                    'updated_at' => $staff->user->updated_at,
                    'roles' => $staff->user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                            'guard_name' => $role->guard_name,
                        ];
                    }),
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
