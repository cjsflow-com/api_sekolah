<?php

namespace App\Http\Requests\Roles;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => trim(
                    (string) $this->input('name')
                ),
            ]);
        }
    }

    public function rules(): array
    {
        /** @var Role $role */
        $role = $this->route('role');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',

                Rule::unique(
                    'roles',
                    'name'
                )->ignore($role),
            ],

            'permission_ids' => [
                'sometimes',
                'array',
            ],

            'permission_ids.*' => [
                'integer',
                'distinct',

                Rule::exists(
                    'permissions',
                    'id'
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'Nama role wajib diisi.',

            'name.unique' =>
                'Nama role sudah digunakan.',

            'permission_ids.array' =>
                'Permission harus berupa array.',

            'permission_ids.*.distinct' =>
                'Permission tidak boleh duplikat.',

            'permission_ids.*.exists' =>
                'Permission yang dipilih tidak ditemukan.',
        ];
    }
}