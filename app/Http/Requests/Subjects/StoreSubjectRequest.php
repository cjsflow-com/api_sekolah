<?php

namespace App\Http\Requests\Subjects;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
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
                'unique:subjects,code',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' =>
                'Kode mata pelajaran wajib diisi.',

            'code.string' =>
                'Kode mata pelajaran harus berupa teks.',

            'code.max' =>
                'Kode mata pelajaran maksimal 50 karakter.',

            'code.unique' =>
                'Kode mata pelajaran sudah digunakan.',

            'name.required' =>
                'Nama mata pelajaran wajib diisi.',

            'name.string' =>
                'Nama mata pelajaran harus berupa teks.',

            'name.max' =>
                'Nama mata pelajaran maksimal 255 karakter.',

            'description.string' =>
                'Deskripsi harus berupa teks.',
        ];
    }
}