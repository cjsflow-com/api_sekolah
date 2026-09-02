<?php

namespace App\Http\Requests\AcademicYears;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:academic_years,name',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
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