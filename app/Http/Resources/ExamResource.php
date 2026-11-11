<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'class'           => $this->whenLoaded('schoolClass', fn () => $this->schoolClass?->name),
            'academic_year'   => $this->whenLoaded('academicYear', fn () => $this->academicYear?->name),
            'start_date'      => $this->start_date?->toDateString(),
            'end_date'        => $this->end_date?->toDateString(),
            'is_published'    => $this->is_published,
            'marks_count'     => $this->when($this->relationLoaded('marks'), fn () => $this->marks->count()),
        ];
    }
}
