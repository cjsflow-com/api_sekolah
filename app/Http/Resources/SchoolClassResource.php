<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'academic_year_id' => $this->academic_year_id,
            'academic_year' => $this->whenLoaded(
                'academicYear'
            ),

            'education_unit_id' => $this->education_unit_id,
            'education_unit' => $this->whenLoaded(
                'educationUnit'
            ),

            'name' => $this->name,
            'level' => $this->level,

            'homeroom_teacher_id' => $this->homeroom_teacher_id,
            'homeroom_teacher' => $this->whenLoaded(
                'homeroomTeacher'
            ),

            'capacity' => $this->capacity,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
