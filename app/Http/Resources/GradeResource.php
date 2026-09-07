<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'student_id' => $this->student_id,
            'student' => $this->whenLoaded('student'),

            'teaching_assignment_id' =>
                $this->teaching_assignment_id,

            'teaching_assignment' => $this->whenLoaded(
                'teachingAssignment'
            ),

            'grade_component_id' =>
                $this->grade_component_id,

            'grade_component' => $this->whenLoaded(
                'gradeComponent'
            ),

            'score' => $this->score,

            'recorded_by' => $this->recorded_by,

            'recorded_by_teacher' => $this->whenLoaded(
                'recordedBy'
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}