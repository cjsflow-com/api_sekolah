<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareValidation():void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => Str::lower(
                trim((string) $this->input('email'))
            ),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',  Rule::unique('users', 'email'),],
            'password' => ['required','confirmed', Password::min(8)->letters()->numbers()],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id'),
            ],
             'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists' => 'Role yang dipilih tidak ditemukan.',
            'avatar.image' => 'Avatar harus berupa gambar.',
            'avatar.max' => 'Ukuran avatar maksimal 2 MB.',
        ];
    }
}
