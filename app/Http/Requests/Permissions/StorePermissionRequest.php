<?php

namespace App\Http\Requests\Permissions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends FormRequest
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
        return [
            'module' => [
                'required',
                'string',
                'max:100',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'action' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'permissions',
                    'action'
                )->where(
                    fn ($query) =>
                        $query->where(
                            'module',
                            $this->input(
                                'module'
                            )
                        )
                ),
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