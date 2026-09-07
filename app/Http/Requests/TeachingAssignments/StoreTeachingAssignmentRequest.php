<?php

namespace App\Http\Requests\TeachingAssignments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeachingAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => [
                'required',
                'integer',
                'exists:teachers,id',
            ],

            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',

                Rule::unique(
                    'teaching_assignments',
                    'subject_id'
                )->where(
                    fn ($query) => $query
                        ->where(
                            'teacher_id',
                            $this->integer('teacher_id')
                        )
                        ->where(
                            'school_class_id',
                            $this->integer('school_class_id')
                        )
                        ->where(
                            'semester_id',
                            $this->integer('semester_id')
                        )
                ),
            ],

            'school_class_id' => [
                'required',
                'integer',
                'exists:school_classes,id',
            ],

            'semester_id' => [
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