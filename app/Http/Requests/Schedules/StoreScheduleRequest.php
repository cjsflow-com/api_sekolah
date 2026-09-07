<?php

namespace App\Http\Requests\Schedules;

use App\Models\Schedule;
use App\Models\TeachingAssignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teaching_assignment_id' => [
                'required',
                'integer',
                'exists:teaching_assignments,id',
            ],

            'class_room_id' => [
                'required',
                'integer',
                'exists:class_rooms,id',
            ],

            'day' => [
                'required',
                'string',
                Rule::in([
                    Schedule::DAY_MONDAY,
                    Schedule::DAY_TUESDAY,
                    Schedule::DAY_WEDNESDAY,
                    Schedule::DAY_THURSDAY,
                    Schedule::DAY_FRIDAY,
                    Schedule::DAY_SATURDAY,
                ]),
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $teachingAssignment = TeachingAssignment::query()
                    ->find(
                        $this->integer('teaching_assignment_id')
                    );

                if (! $teachingAssignment) {
                    return;
                }

                $day = (string) $this->input('day');
                $startTime = (string) $this->input('start_time');
                $endTime = (string) $this->input('end_time');
                $classRoomId = $this->integer('class_room_id');

                $conflictQuery = Schedule::query()
                    ->where('day', $day)
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime)
                    ->whereHas(
                        'teachingAssignment',
                        fn ($query) => $query->where(
                            'semester_id',
                            $teachingAssignment->semester_id
                        )
                    );

                $roomConflict = (clone $conflictQuery)
                    ->where(
                        'class_room_id',
                        $classRoomId
                    )
                    ->exists();

                if ($roomConflict) {
                    $validator->errors()->add(
                        'class_room_id',
                        'Ruangan sudah digunakan pada hari dan waktu tersebut.'
                    );
                }

                $teacherConflict = (clone $conflictQuery)
                    ->whereHas(
                        'teachingAssignment',
                        fn ($query) => $query->where(
                            'teacher_id',
                            $teachingAssignment->teacher_id
                        )
                    )
                    ->exists();

                if ($teacherConflict) {
                    $validator->errors()->add(
                        'teaching_assignment_id',
                        'Guru sudah memiliki jadwal pada hari dan waktu tersebut.'
                    );
                }

                $classConflict = (clone $conflictQuery)
                    ->whereHas(
                        'teachingAssignment',
                        fn ($query) => $query->where(
                            'school_class_id',
                            $teachingAssignment->school_class_id
                        )
                    )
                    ->exists();

                if ($classConflict) {
                    $validator->errors()->add(
                        'teaching_assignment_id',
                        'Kelas sudah memiliki jadwal pada hari dan waktu tersebut.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'teaching_assignment_id.required' =>
                'Penugasan mengajar wajib dipilih.',

            'teaching_assignment_id.integer' =>
                'Penugasan mengajar tidak valid.',

            'teaching_assignment_id.exists' =>
                'Penugasan mengajar tidak ditemukan.',

            'class_room_id.required' =>
                'Ruangan kelas wajib dipilih.',

            'class_room_id.integer' =>
                'Ruangan kelas tidak valid.',

            'class_room_id.exists' =>
                'Ruangan kelas tidak ditemukan.',

            'day.required' =>
                'Hari wajib dipilih.',

            'day.string' =>
                'Hari harus berupa teks.',

            'day.in' =>
                'Hari yang dipilih tidak valid.',

            'start_time.required' =>
                'Jam mulai wajib diisi.',

            'start_time.date_format' =>
                'Format jam mulai harus HH:mm.',

            'end_time.required' =>
                'Jam selesai wajib diisi.',

            'end_time.date_format' =>
                'Format jam selesai harus HH:mm.',

            'end_time.after' =>
                'Jam selesai harus setelah jam mulai.',
        ];
    }
}