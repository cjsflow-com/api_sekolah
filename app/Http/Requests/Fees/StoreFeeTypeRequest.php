<?php

namespace App\Http\Requests\Fees;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreFeeTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:fee_types,name',
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
            'name.required' => 'Nama jenis biaya wajib diisi',
            'name.string' => 'Nama jenis biaya harus berupa teks',
            'name.max' => 'Nama jenis biaya maksimal 255 karakter',
            'name.unique' => 'Nama jenis biaya sudah digunakan',

            'is_active.boolean' => 'Status aktif harus berupa true dan false.'
        ];
    }
}
