<?php

namespace App\Http\Requests\StudentAttendances;

use Illuminate\Foundation\Http\FormRequest;

class StudentAttendanceSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'semester_id' => [
                'nullable',
                'integer',
                'exists:semesters,id',
            ],

            'academic_year_id' => [
                'nullable',
                'integer',
                'exists:academic_years,id',
            ],

            'subject_id' => [
                'nullable',
                'integer',
                'exists:subjects,id',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'semester_id.integer' =>
                'Semester tidak valid.',

            'semester_id.exists' =>
                'Semester tidak ditemukan.',

            'academic_year_id.integer' =>
                'Tahun ajaran tidak valid.',

            'academic_year_id.exists' =>
                'Tahun ajaran tidak ditemukan.',

            'subject_id.integer' =>
                'Mata pelajaran tidak valid.',

            'subject_id.exists' =>
                'Mata pelajaran tidak ditemukan.',

            'per_page.integer' =>
                'Jumlah data per halaman harus berupa angka.',

            'per_page.min' =>
                'Jumlah data per halaman minimal 1.',

            'per_page.max' =>
                'Jumlah data per halaman maksimal 100.',
        ];
    }
}