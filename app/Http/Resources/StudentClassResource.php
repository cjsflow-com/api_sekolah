<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'student_id' => $this->student_id,
            'student' => $this->whenLoaded('student'),

            'school_class_id' => $this->school_class_id,
            'school_class' => $this->whenLoaded(
                'schoolClass'
            ),

            'academic_year_id' => $this->academic_year_id,
            'academic_year' => $this->whenLoaded(
                'academicYear'
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}