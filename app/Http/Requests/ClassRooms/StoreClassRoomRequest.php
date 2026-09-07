<?php

namespace App\Http\Requests\ClassRooms;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:class_rooms,code',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'capacity' => [
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