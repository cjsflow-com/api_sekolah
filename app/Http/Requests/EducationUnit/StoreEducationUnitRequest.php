<?php

namespace App\Http\Requests\EducationUnits;

use Illuminate\Foundation\Http\FormRequest;

class StoreEducationUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:education_units,code',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
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
            'code.required' => 'Kode satuan pendidikan wajib diisi.',

            'code.string' => 'Kode satuan pendidikan harus berupa teks.',

            'code.max' => 'Kode satuan pendidikan maksimal 50 karakter.',

            'code.unique' => 'Kode satuan pendidikan sudah digunakan.',

            'name.required' => 'Nama satuan pendidikan wajib diisi.',

            'name.string' => 'Nama satuan pendidikan harus berupa teks.',

            'name.max' => 'Nama satuan pendidikan maksimal 255 karakter.',

            'is_active.boolean' => 'Status aktif harus berupa true atau false.',
        ];
    }
}
