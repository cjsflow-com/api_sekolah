<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'schedule_id' => $this->schedule_id,

            'schedule' => $this->whenLoaded(
                'schedule'
            ),

            'meeting_no' => $this->meeting_no,

            'meeting_date' => $this->meeting_date?->format(
                'Y-m-d'
            ),

            'topic' => $this->topic,

            'class_attendances' => $this->whenLoaded(
                'classAttendances'
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
