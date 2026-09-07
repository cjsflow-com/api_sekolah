<?php

namespace App\Http\Requests\TeachingAssignments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeachingAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teachingAssignment = $this->route(
            'teaching_assignment'
        );

        $teacherId = $this->integer(
            'teacher_id',
            $teachingAssignment->teacher_id
        );

        $schoolClassId = $this->integer(
            'school_class_id',
            $teachingAssignment->school_class_id
        );

        $semesterId = $this->integer(
            'semester_id',
            $teachingAssignment->semester_id
        );

        return [
            'teacher_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:teachers,id',
            ],

            'subject_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:subjects,id',

                Rule::unique(
                    'teaching_assignments',
                    'subject_id'
                )
                    ->where(
                        fn ($query) => $query
                            ->where(
                                'teacher_id',
                                $teacherId
                            )
                            ->where(
                                'school_class_id',
                                $schoolClassId
                            )
                            ->where(
                                'semester_id',
                                $semesterId
                            )
                    )
                    ->ignore($teachingAssignment),
            ],

            'school_class_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:school_classes,id',
            ],

            'semester_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:semesters,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'teacher_id.required' =>
                'Guru wajib dipilih.',

            'teacher_id.exists' =>
                'Guru tidak ditemukan.',

            'subject_id.required' =>
                'Mata pelajaran wajib dipilih.',

            'subject_id.exists' =>
                'Mata pelajaran tidak ditemukan.',

            'subject_id.unique' =>
                'Penugasan mengajar tersebut sudah tersedia.',

            'school_class_id.required' =>
                'Kelas wajib dipilih.',

            'school_class_id.exists' =>
                'Kelas tidak ditemukan.',

            'semester_id.required' =>
                'Semester wajib dipilih.',

            'semester_id.exists' =>
                'Semester tidak ditemukan.',
        ];
    }
}