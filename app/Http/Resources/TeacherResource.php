<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'employee_id'  => $this->employee_id,
            'name'         => $this->full_name(),
            'gender'       => $this->gender,
            'phone'        => $this->phone,
            'email'        => $this->email,
            'qualification' => $this->qualification,
            'is_active'    => $this->is_active,
            'join_date'    => $this->join_date?->toDateString(),
        ];
    }
}
