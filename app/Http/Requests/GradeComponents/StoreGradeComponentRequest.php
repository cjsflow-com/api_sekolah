<?php

namespace App\Http\Requests\GradeComponents;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeComponentRequest extends FormRequest
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
                'unique:grade_components,name',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'Nama komponen nilai wajib diisi.',

            'name.string' =>
                'Nama komponen nilai harus berupa teks.',

            'name.max' =>
                'Nama komponen nilai maksimal 255 karakter.',

            'name.unique' =>
                'Nama komponen nilai sudah digunakan.',
        ];
    }
}