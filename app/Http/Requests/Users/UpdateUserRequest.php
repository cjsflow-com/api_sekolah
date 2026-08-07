<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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

        $this->merge($data);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
     public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',

                Rule::unique('users', 'email')
                    ->ignore($user),
            ],

            'password' => [
                'sometimes',
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],

            'role_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('roles', 'id'),
            ],

            'is_active' => [
                'sometimes',
                'boolean',
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
}
