<?php

namespace App\Http\Requests\SchoolClasses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSchoolClassRequest extends FormRequest
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

            'education_unit_id' => [
                'required',
                'integer',
                'exists:education_units,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('school_classes')
                    ->where(
                        fn ($query) => $query
                            ->where(
                                'academic_year_id',
                                $this->integer('academic_year_id')
                            )
                            ->where(
                                'education_unit_id',
                                $this->integer('education_unit_id')
                            )
                    ),
            ],

            'level' => [
                'required',
                'integer',
                'min:1',
            ],

            'homeroom_teacher_id' => [
                'nullable',
                'integer',
                'exists:teachers,id',
            ],

            'capacity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'academic_year_id.required' => 'Tahun ajaran wajib dipilih.',

            'academic_year_id.exists' => 'Tahun ajaran tidak ditemukan.',

            'education_unit_id.required' => 'Satuan pendidikan wajib dipilih.',

            'education_unit_id.exists' => 'Satuan pendidikan tidak ditemukan.',

            'name.required' => 'Nama kelas wajib diisi.',

            'name.string' => 'Nama kelas harus berupa teks.',

            'name.max' => 'Nama kelas maksimal 255 karakter.',

            'name.unique' => 'Nama kelas sudah digunakan pada tahun ajaran dan satuan pendidikan tersebut.',

            'level.required' => 'Tingkat kelas wajib diisi.',

            'level.integer' => 'Tingkat kelas harus berupa angka.',

            'level.min' => 'Tingkat kelas minimal 1.',

            'homeroom_teacher_id.exists' => 'Guru wali kelas tidak ditemukan.',

            'capacity.required' => 'Kapasitas kelas wajib diisi.',

            'capacity.integer' => 'Kapasitas kelas harus berupa angka.',

            'capacity.min' => 'Kapasitas kelas minimal 1 siswa.',
        ];
    }
}
