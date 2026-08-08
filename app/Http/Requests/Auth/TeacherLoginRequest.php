<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class TeacherLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('identity_number')) {
            $this->merge([
                'identity_number' => trim(
                    (string) $this->input('identity_number')
                ),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'identity_number' => [
                'required',
                'string',
                'max:18',
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
            'identity_number.required' =>
                'Nomor identitas wajib diisi.',

            'identity_number.max' =>
                'Nomor identitas tidak boleh melebihi 18 karakter.',

            'password.required' =>
                'Password wajib diisi.',
        ];
    }
}
