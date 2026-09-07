<?php

namespace App\Http\Requests\StudentClasses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',

                Rule::unique(
                    'student_classes',
                    'student_id'
                )->where(
                    fn ($query) => $query->where(
                        'academic_year_id',
                        $this->integer('academic_year_id')
                    )
                ),
            ],

            'school_class_id' => [
                'required',
                'integer',
                'exists:school_classes,id',
            ],

            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' =>
                'Siswa wajib dipilih.',

            'student_id.exists' =>
                'Siswa tidak ditemukan.',

            'student_id.unique' =>
                'Siswa sudah memiliki kelas pada tahun ajaran tersebut.',

            'school_class_id.required' =>
                'Kelas wajib dipilih.',

            'school_class_id.exists' =>
                'Kelas tidak ditemukan.',

            'academic_year_id.required' =>
                'Tahun ajaran wajib dipilih.',

            'academic_year_id.exists' =>
                'Tahun ajaran tidak ditemukan.',
        ];
    }
}