<?php

namespace App\Http\Requests\ClassRooms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClassRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $classRoom = $this->route('class_room');

        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique(
                    'class_rooms',
                    'code'
                )->ignore($classRoom),
            ],

            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'capacity' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' =>
                'Kode ruangan wajib diisi.',

            'code.string' =>
                'Kode ruangan harus berupa teks.',

            'code.max' =>
                'Kode ruangan maksimal 50 karakter.',

            'code.unique' =>
                'Kode ruangan sudah digunakan.',

            'name.required' =>
                'Nama ruangan wajib diisi.',

            'name.string' =>
                'Nama ruangan harus berupa teks.',

            'name.max' =>
                'Nama ruangan maksimal 255 karakter.',

            'capacity.required' =>
                'Kapasitas ruangan wajib diisi.',

            'capacity.integer' =>
                'Kapasitas ruangan harus berupa angka.',

            'capacity.min' =>
                'Kapasitas ruangan minimal 1 orang.',
        ];
    }
}