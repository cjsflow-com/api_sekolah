<?php

namespace App\Http\Requests\Subjects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subject = $this->route('subject');

        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique(
                    'subjects',
                    'code'
                )->ignore($subject),
            ],

            'name' => [
                'sometimes',
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