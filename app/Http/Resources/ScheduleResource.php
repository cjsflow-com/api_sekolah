<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'teaching_assignment_id' =>
                $this->teaching_assignment_id,

            'teaching_assignment' => $this->whenLoaded(
                'teachingAssignment'
            ),

            'class_room_id' => $this->class_room_id,

            'class_room' => $this->whenLoaded(
                'classRoom'
            ),

            'day' => $this->day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}