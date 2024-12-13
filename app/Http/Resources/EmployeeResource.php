<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'enterprise' => [
                'id' => $this->enterprise->id,
                'name' => $this->enterprise->user->name,
            ],
            'code' => $this->code,
            'name' => $this->name,
            'avatar' => env('APP_URL') . '/' . $this->avatar,
            'email' => $this->email,
            'phone' => $this->phone,
            'birthday' => $this->birthday,
            'taxcode' => $this->taxcode,
            'education_level' => $this->education_level,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'salary' => $this->salary,
            'address' => $this->address,
            'status' => $this->status,
        ];
    }
}
