<?php

namespace App\Http\Requests\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name'),
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

            'permission_ids.*.integer' =>
                'ID permission tidak valid.',

            'permission_ids.*.distinct' =>
                'Permission tidak boleh duplikat.',

            'permission_ids.*.exists' =>
                'Permission yang dipilih tidak ditemukan.',
        ];
    }
}