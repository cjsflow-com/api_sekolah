<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeachingAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'teacher_id' => $this->teacher_id,
            'teacher' => $this->whenLoaded('teacher'),

            'subject_id' => $this->subject_id,
            'subject' => $this->whenLoaded('subject'),

            'school_class_id' => $this->school_class_id,
            'school_class' => $this->whenLoaded(
                'schoolClass'
            ),

            'semester_id' => $this->semester_id,
            'semester' => $this->whenLoaded(
                'semester'
            ),

            'grade_components' => $this->whenLoaded(
                'gradeComponents'
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}