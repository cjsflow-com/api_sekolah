<?php

namespace App\Http\Requests\AcademicYears;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $academicYear = $this->route(
            'academic_year'
        );

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'academic_years',
                    'name'
                )->ignore($academicYear),
            ],

            'start_date' => [
                'sometimes',
                'required',
                'date',
            ],

            'end_date' => [
                'sometimes',
                'required',
                'date',
                'after_or_equal:start_date',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'Nama tahun ajaran wajib diisi.',

            'name.string' =>
                'Nama tahun ajaran harus berupa teks.',

            'name.max' =>
                'Nama tahun ajaran maksimal 255 karakter.',

            'name.unique' =>
                'Nama tahun ajaran sudah digunakan.',

            'start_date.required' =>
                'Tanggal mulai tahun ajaran wajib diisi.',

            'start_date.date' =>
                'Tanggal mulai tahun ajaran tidak valid.',

            'end_date.required' =>
                'Tanggal selesai tahun ajaran wajib diisi.',

            'end_date.date' =>
                'Tanggal selesai tahun ajaran tidak valid.',

            'end_date.after_or_equal' =>
                'Tanggal selesai tidak boleh sebelum tanggal mulai.',

            'is_active.boolean' =>
                'Status aktif harus berupa true atau false.',
        ];
    }
}