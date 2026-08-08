<?php

namespace App\Http\Requests\Students;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateStudentRequest extends FormRequest
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

        if ($this->has('nis')) {
            $data['nis'] = trim(
                (string) $this->input('nis')
            );
        }

        if ($this->has('nisn')) {
            $nisn = trim(
                (string) $this->input('nisn')
            );

            $data['nisn'] = $nisn !== ''
                ? $nisn
                : null;
        }

        if ($this->has('email')) {
            $email = Str::lower(
                trim(
                    (string) $this->input('email')
                )
            );

            $data['email'] = $email !== ''
                ? $email
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
        /** @var Student $student */
        $student = $this->route('student');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'nis' => [
                'sometimes',
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'students',
                    'nis'
                )->ignore($student),
            ],

            'nisn' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',

                Rule::unique(
                    'students',
                    'nisn'
                )->ignore($student),
            ],

            'email' => [
                'sometimes',
                'nullable',
                'string',
                'email',
                'max:255',

                Rule::unique(
                    'students',
                    'email'
                )->ignore($student),
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
                    Student::GENDER_MALE,
                    Student::GENDER_FEMALE,
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

            'parent_name' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'parent_phone' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
            ],

            'status' => [
                'sometimes',

                Rule::in([
                    Student::STATUS_ACTIVE,
                    Student::STATUS_GRADUATED,
                    Student::STATUS_MOVED,
                    Student::STATUS_INACTIVE,
                ]),
            ],

            'avatar' => [
                'sometimes',
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
            'name.required' =>
                'Nama siswa wajib diisi.',

            'nis.required' =>
                'NIS wajib diisi.',

            'nis.unique' =>
                'NIS sudah digunakan.',

            'nisn.unique' =>
                'NISN sudah digunakan.',

            'email.email' =>
                'Format email tidak valid.',

            'email.unique' =>
                'Email sudah digunakan.',

            'password.confirmed' =>
                'Konfirmasi password tidak sesuai.',

            'gender.in' =>
                'Jenis kelamin tidak valid.',

            'status.in' =>
                'Status siswa tidak valid.',

            'birth_date.before_or_equal' =>
                'Tanggal lahir tidak boleh melebihi hari ini.',

            'avatar.image' =>
                'Avatar harus berupa gambar.',

            'avatar.max' =>
                'Ukuran avatar maksimal 2 MB.',
        ];
    }
}
