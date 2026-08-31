<?php

namespace App\Http\Requests\Semesters;

use App\Models\Semester;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSemesterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',
            ],

            'name' => [
                'required',
                Rule::in([
                    Semester::NAME_ODD,
                    Semester::NAME_EVEN,
                ]),
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
            'academic_year_id.required' => 'Tahun ajaran wajib dipilih.',

            'academic_year_id.exists' => 'Tahun ajaran tidak ditemukan.',

            'name.required' => 'Nama semester wajib dipilih.',

            'name.in' => 'Nama semester harus Ganjil atau Genap.',

            'start_date.required' => 'Tanggal mulai semester wajib diisi.',

            'start_date.date' => 'Tanggal mulai semester tidak valid.',

            'end_date.required' => 'Tanggal selesai semester wajib diisi.',

            'end_date.date' => 'Tanggal selesai semester tidak valid.',

            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',

            'is_active.boolean' => 'Status aktif harus berupa true atau false.',
        ];
    }
}
