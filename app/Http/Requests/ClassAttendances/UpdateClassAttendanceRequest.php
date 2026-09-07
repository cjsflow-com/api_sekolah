<?php

namespace App\Http\Requests\ClassAttendances;

use App\Models\ClassAttendance;
use App\Models\StudentClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateClassAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attendances' => [
                'required',
                'array',
                'min:1',
            ],

            'attendances.*.student_id' => [
                'required',
                'integer',
                'distinct',
                'exists:students,id',
            ],

            'attendances.*.status' => [
                'required',
                'string',
                Rule::in([
                    ClassAttendance::STATUS_PRESENT,
                    ClassAttendance::STATUS_SICK,
                    ClassAttendance::STATUS_EXCUSED,
                    ClassAttendance::STATUS_ABSENT,
                    ClassAttendance::STATUS_LATE,
                ]),
            ],

            'attendances.*.note' => [
                'nullable',
                'string',
                'max:500',
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

                $attendanceSession = $this->route(
                    'attendance_session'
                );

                $attendanceSession->loadMissing(
                    'schedule.teachingAssignment.schoolClass'
                );

                $schoolClass = $attendanceSession
                    ->schedule
                    ?->teachingAssignment
                    ?->schoolClass;

                if (! $schoolClass) {
                    $validator->errors()->add(
                        'attendances',
                        'Kelas dari sesi absensi tidak ditemukan.'
                    );

                    return;
                }

                foreach (
                    $this->input('attendances', [])
                    as $index => $attendance
                ) {
                    $studentId = (int) $attendance[
                        'student_id'
                    ];

                    $isRegistered = StudentClass::query()
                        ->where(
                            'student_id',
                            $studentId
                        )
                        ->where(
                            'school_class_id',
                            $schoolClass->id
                        )
                        ->where(
                            'academic_year_id',
                            $schoolClass->academic_year_id
                        )
                        ->exists();

                    if (! $isRegistered) {
                        $validator->errors()->add(
                            "attendances.{$index}.student_id",
                            'Siswa tidak terdaftar pada kelas ini.'
                        );
                    }
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'attendances.required' =>
                'Data absensi wajib diisi.',

            'attendances.array' =>
                'Data absensi harus berupa array.',

            'attendances.min' =>
                'Minimal terdapat satu data absensi.',

            'attendances.*.student_id.required' =>
                'Siswa wajib dipilih.',

            'attendances.*.student_id.integer' =>
                'Siswa tidak valid.',

            'attendances.*.student_id.distinct' =>
                'Siswa tidak boleh dikirim lebih dari satu kali.',

            'attendances.*.student_id.exists' =>
                'Siswa tidak ditemukan.',

            'attendances.*.status.required' =>
                'Status kehadiran wajib dipilih.',

            'attendances.*.status.in' =>
                'Status kehadiran tidak valid.',

            'attendances.*.note.string' =>
                'Catatan harus berupa teks.',

            'attendances.*.note.max' =>
                'Catatan maksimal 500 karakter.',
        ];
    }
}