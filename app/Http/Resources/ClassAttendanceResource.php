<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassAttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'attendance_session_id' =>
                $this->attendance_session_id,

            'student_id' => $this->student_id,

            'student' => $this->whenLoaded(
                'student'
            ),

            'status' => $this->status,

            'note' => $this->note,

            'recorded_by' => $this->recorded_by,

            'teacher' => $this->whenLoaded(
                'recordedBy'
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}