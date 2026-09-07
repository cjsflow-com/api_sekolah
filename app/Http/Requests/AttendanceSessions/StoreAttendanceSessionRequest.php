<?php

namespace App\Http\Requests\AttendanceSessions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'schedule_id' => [
                'required',
                'integer',
                'exists:schedules,id',
            ],

            'meeting_no' => [
                'required',
                'integer',
                'min:1',

                Rule::unique(
                    'attendance_sessions',
                    'meeting_no'
                )->where(
                    fn ($query) => $query->where(
                        'schedule_id',
                        $this->integer('schedule_id')
                    )
                ),
            ],

            'meeting_date' => [
                'required',
                'date',
            ],

            'topic' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_id.required' =>
                'Jadwal wajib dipilih.',

            'schedule_id.exists' =>
                'Jadwal tidak ditemukan.',

            'meeting_no.required' =>
                'Nomor pertemuan wajib diisi.',

            'meeting_no.integer' =>
                'Nomor pertemuan harus berupa angka.',

            'meeting_no.min' =>
                'Nomor pertemuan minimal 1.',

            'meeting_no.unique' =>
                'Nomor pertemuan sudah digunakan pada jadwal tersebut.',

            'meeting_date.required' =>
                'Tanggal pertemuan wajib diisi.',

            'meeting_date.date' =>
                'Tanggal pertemuan tidak valid.',

            'topic.string' =>
                'Topik pertemuan harus berupa teks.',

            'topic.max' =>
                'Topik pertemuan maksimal 255 karakter.',
        ];
    }
}
