<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmployeeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'enterprise' => [
                        'id' => $employee->enterprise->id,
                        'name' => $employee->enterprise->user->name,
                    ],
                    'code' => $employee->code,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'birthday' => $employee->birthday,
                    'taxcode' => $employee->taxcode,
                    'education_level' => $employee->education_level,
                    'start_date' => $employee->start_date,
                    'end_date' => $employee->end_date,
                    'salary' => $employee->salary,
                    'address' => $employee->address,
                    'status' => $employee->status,
                ];
            })
        ];
    }
}
