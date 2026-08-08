<?php

namespace App\Http\Requests\Teachers;

use App\Models\Teacher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('name')) {
            $data['name'] = trim(
                (string) $this->input('name')
            );
        }

        if ($this->has('email')) {
            $data['email'] = Str::lower(
                trim((string) $this->input('email'))
            );
        }

        if ($this->has('identity_number')) {
            $identityNumber = trim(
                (string) $this->input('identity_number')
            );

            $data['identity_number'] =
                $identityNumber !== ''
                    ? $identityNumber
                    : null;
        }

        if ($this->has('phone')) {
            $phone = trim(
                (string) $this->input('phone')
            );

            $data['phone'] = $phone !== ''
                ? $phone
                : null;
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        /** @var Teacher $teacher */
        $teacher = $this->route('teacher');

        return [
            'identity_type' => [
                'sometimes',
                'nullable',

                Rule::in([
                    Teacher::IDENTITY_TYPE_NIP,
                    Teacher::IDENTITY_TYPE_NUPTK,
                ]),
            ],

            'identity_number' => [
                'sometimes',
                'nullable',
                'string',
                'max:18',

                Rule::unique(
                    'teachers',
                    'identity_number'
                )->ignore($teacher),
            ],

            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',

                Rule::unique(
                    'teachers',
                    'email'
                )->ignore($teacher),
            ],

            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'sometimes',
                'nullable',
                'confirmed',

                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],

            'gender' => [
                'sometimes',
                'nullable',
                'integer',

                Rule::in([
                    Teacher::GENDER_MALE,
                    Teacher::GENDER_FEMALE,
                ]),
            ],

            'birth_place' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'birth_date' => [
                'sometimes',
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'address' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'avatar' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
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
            'identity_type.in' =>
                'Jenis identitas harus NIP atau NUPTK.',

            'identity_number.unique' =>
                'Nomor identitas sudah digunakan.',

            'name.required' =>
                'Nama guru wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'email.unique' =>
                'Email sudah digunakan.',

            'password.confirmed' =>
                'Konfirmasi password tidak sesuai.',

            'gender.in' =>
                'Jenis kelamin tidak valid.',

            'birth_date.before_or_equal' =>
                'Tanggal lahir tidak boleh melebihi hari ini.',

            'avatar.image' =>
                'Avatar harus berupa gambar.',

            'avatar.mimes' =>
                'Avatar harus berformat JPG, JPEG, PNG, atau WEBP.',

            'avatar.max' =>
                'Ukuran avatar maksimal 2 MB.',
        ];
    }
}
