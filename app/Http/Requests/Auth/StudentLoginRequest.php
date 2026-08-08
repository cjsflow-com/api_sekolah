<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StudentLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nis')) {
            $this->merge([
                'nis' => trim(
                    (string) $this->input('nis')
                ),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nis' => [
                'required',
                'string',
                'max:50',
            ],

            'password' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required' =>
                'NIS wajib diisi.',

            'password.required' =>
                'Password wajib diisi.',
        ];
    }
}
