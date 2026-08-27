<?php

namespace App\Http\Requests\Permissions;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('module')) {
            $data['module'] = Str::lower(
                trim(
                    (string) $this->input(
                        'module'
                    )
                )
            );
        }

        if ($this->has('action')) {
            $data['action'] = Str::lower(
                trim(
                    (string) $this->input(
                        'action'
                    )
                )
            );
        }

        if ($this->has('name')) {
            $data['name'] = trim(
                (string) $this->input(
                    'name'
                )
            );
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        /** @var Permission $permission */
        $permission = $this->route(
            'permission'
        );

        $module = $this->input(
            'module',
            $permission->module
        );

        return [
            'module' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],

            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'action' => [
                'sometimes',
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'permissions',
                    'action'
                )
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'module',
                                $module
                            )
                    )
                    ->ignore($permission),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'module.required' =>
                'Module permission wajib diisi.',

            'name.required' =>
                'Nama permission wajib diisi.',

            'action.required' =>
                'Action permission wajib diisi.',

            'action.unique' =>
                'Permission untuk module dan action tersebut sudah tersedia.',
        ];
    }
}