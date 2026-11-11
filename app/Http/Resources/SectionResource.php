<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'capacity'   => $this->capacity,
            'class_teacher' => $this->whenLoaded('classTeacher', fn () => $this->classTeacher?->full_name()),
            'students_count' => $this->when(
                $this->relationLoaded('activeEnrollments'),
                fn () => $this->activeEnrollments->count()
            ),
        ];
    }
}
