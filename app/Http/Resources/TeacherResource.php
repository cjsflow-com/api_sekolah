<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TeacherResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {
        return [
            'id' => $this->id,

            'identity' => [
                'type' => $this->identity_type,
                'number' => $this->identity_number,
            ],

            'name' => $this->name,

            'email' => $this->email,

            'phone' => $this->phone,

            'gender' => $this->gender,

            'gender_label' => match ($this->gender) {
                1 => 'Laki-laki',
                2 => 'Perempuan',
                default => null,
            },

            'birth_place' => $this->birth_place,

            'birth_date' => $this->birth_date
                ?->format('Y-m-d'),

            'address' => $this->address,

            'avatar' => $this->avatar,

            'avatar_url' => $this->avatar
                ? Storage::disk('public')
                    ->url($this->avatar)
                : null,

            'is_active' => $this->is_active,

            /*
            |--------------------------------------------------------------------------
            | Status Wali Kelas
            |--------------------------------------------------------------------------
            */

            'is_homeroom_teacher' => $this->whenLoaded(
                'homeroomClasses',
                fn () => $this->homeroomClasses->isNotEmpty()
            ),

            /*
            |--------------------------------------------------------------------------
            | Kelas yang menjadi tanggung jawab sebagai wali kelas
            |--------------------------------------------------------------------------
            */

            'homeroom_classes' => $this->whenLoaded(
                'homeroomClasses',
                fn () => $this->homeroomClasses
                    ->map(
                        fn ($schoolClass) => [
                            'id' => $schoolClass->id,

                            'name' => $schoolClass->name,

                            'level' => $schoolClass->level,

                            'capacity' => $schoolClass->capacity,

                            'academic_year_id' =>
                                $schoolClass->academic_year_id,

                            'academic_year' =>
                                $schoolClass->relationLoaded(
                                    'academicYear'
                                )
                                    ? [
                                        'id' =>
                                            $schoolClass
                                                ->academicYear
                                                ?->id,

                                        'name' =>
                                            $schoolClass
                                                ->academicYear
                                                ?->name,
                                    ]
                                    : null,

                            'education_unit_id' =>
                                $schoolClass->education_unit_id,

                            'education_unit' =>
                                $schoolClass->relationLoaded(
                                    'educationUnit'
                                )
                                    ? [
                                        'id' =>
                                            $schoolClass
                                                ->educationUnit
                                                ?->id,

                                        'code' =>
                                            $schoolClass
                                                ->educationUnit
                                                ?->code,

                                        'name' =>
                                            $schoolClass
                                                ->educationUnit
                                                ?->name,
                                    ]
                                    : null,
                        ]
                    )
                    ->values()
            ),

            /*
            |--------------------------------------------------------------------------
            | Penugasan Mengajar
            |--------------------------------------------------------------------------
            */

            'teaching_assignments' => $this->whenLoaded(
                'teachingAssignments'
            ),

            /*
            |--------------------------------------------------------------------------
            | Absensi kelas yang dicatat guru
            |--------------------------------------------------------------------------
            */

            'recorded_class_attendances' => $this->whenLoaded(
                'recordedClassAttendances'
            ),

            /*
            |--------------------------------------------------------------------------
            | Nilai yang dicatat guru
            |--------------------------------------------------------------------------
            */

            'recorded_grades' => $this->whenLoaded(
                'recordedGrades'
            ),

            'last_login_at' => $this->last_login_at
                ?->toISOString(),

            'created_at' => $this->created_at
                ?->toISOString(),

            'updated_at' => $this->updated_at
                ?->toISOString(),
        ];
    }
}