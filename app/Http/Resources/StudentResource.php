<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'admission_no'    => $this->admission_no,
            'name'            => $this->full_name(),
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'gender'          => $this->gender,
            'date_of_birth'   => $this->date_of_birth?->toDateString(),
            'phone'           => $this->phone,
            'address'         => $this->address,
            'blood_group'     => $this->blood_group,
            'guardian_name'   => $this->guardian_name,
            'guardian_phone'  => $this->guardian_phone,
            'is_active'       => $this->is_active,
            'class'           => $this->whenLoaded('currentEnrollment', function () {
                $e = $this->currentEnrollment;

                return $e ? [
                    'class'  => $e->schoolClass?->name,
                    'section' => $e->section?->name,
                    'roll_no' => $e->roll_no,
                ] : null;
            }),
            'created_at'      => $this->created_at?->toIso8601String(),
        ];
    }
}
