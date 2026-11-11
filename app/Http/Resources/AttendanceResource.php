<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'student'         => $this->whenLoaded('student', fn () => $this->student?->full_name()),
            'admission_no'    => $this->whenLoaded('student', fn () => $this->student?->admission_no),
            'date'            => $this->attendance_date?->toDateString(),
            'status'          => $this->status,
            'remarks'         => $this->remarks,
        ];
    }
}
