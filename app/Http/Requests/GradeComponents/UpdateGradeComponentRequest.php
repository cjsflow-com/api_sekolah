<?php

namespace App\Http\Requests\GradeComponents;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGradeComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gradeComponent = $this->route(
            'grade_component'
        );

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'grade_components',
                    'name'
                )->ignore($gradeComponent),
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