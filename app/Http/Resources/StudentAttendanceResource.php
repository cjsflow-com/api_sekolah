<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $session = $this->attendanceSession;

        $assignment = $session
            ?->schedule
            ?->teachingAssignment;

        return [
            'id' => $this->id,

            'attendance_session' => [
                'id' => $session?->id,
                'meeting_no' => $session?->meeting_no,
                'meeting_date' => $session
                    ?->meeting_date
                    ?->format('Y-m-d'),
                'topic' => $session?->topic,
            ],

            'subject' => $assignment?->subject
                ? [
                    'id' => $assignment->subject->id,
                    'code' => $assignment->subject->code,
                    'name' => $assignment->subject->name,
                ]
                : null,

            'teacher' => $assignment?->teacher
                ? [
                    'id' => $assignment->teacher->id,
                    'identity_number' =>
                        $assignment->teacher->identity_number,
                    'name' => $assignment->teacher->name,
                ]
                : null,

            'school_class' => $assignment?->schoolClass
                ? [
                    'id' => $assignment->schoolClass->id,
                    'name' => $assignment->schoolClass->name,
                    'level' => $assignment->schoolClass->level,
                ]
                : null,

            'semester' => $assignment?->semester
                ? [
                    'id' => $assignment->semester->id,
                    'name' => $assignment->semester->name,
                ]
                : null,

            'academic_year' => $assignment
                ?->semester
                ?->academicYear
                ? [
                    'id' => $assignment
                        ->semester
                        ->academicYear
                        ->id,

                    'name' => $assignment
                        ->semester
                        ->academicYear
                        ->name,
                ]
                : null,

            'status' => $this->status,
            'note' => $this->note,

            'recorded_by' => $this->recordedBy
                ? [
                    'id' => $this->recordedBy->id,
                    'identity_number' =>
                        $this->recordedBy->identity_number,
                    'name' => $this->recordedBy->name,
                ]
                : null,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}