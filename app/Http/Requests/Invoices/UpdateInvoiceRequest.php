<?php

namespace App\Http\Requests\Invoices;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Invoice $invoice */
        $invoice = $this->route('invoice');

        return [
            'invoice_number' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'invoices',
                    'invoice_number'
                )->ignore($invoice),
            ],

            'student_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:students,id',
            ],

            'fee_type_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:fee_types,id',
            ],

            'semester_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:semesters,id',
            ],

            'month' => [
                'nullable',
                'integer',
                'between:1,12',
            ],

            'year' => [
                'nullable',
                'integer',
                'digits:4',
            ],

            'total_amount' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
            ],

            'amount' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'status' => [
                'sometimes',
                Rule::in([
                    Invoice::STATUS_UNPAID,
                    Invoice::STATUS_PAID,
                    Invoice::STATUS_PARTIAL,
                ]),
            ],

            'due_date' => [
                'nullable',
                'date',
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_number.required' =>
                'Nomor invoice wajib diisi.',
            'invoice_number.unique' =>
                'Nomor invoice sudah digunakan.',

            'student_id.required' =>
                'Siswa wajib dipilih.',
            'student_id.exists' =>
                'Siswa tidak ditemukan.',

            'fee_type_id.required' =>
                'Jenis biaya wajib dipilih.',
            'fee_type_id.exists' =>
                'Jenis biaya tidak ditemukan.',

            'semester_id.required' =>
                'Semester wajib dipilih.',
            'semester_id.exists' =>
                'Semester tidak ditemukan.',

            'month.between' =>
                'Bulan harus antara 1 sampai 12.',

            'year.digits' =>
                'Tahun harus terdiri dari 4 digit.',

            'total_amount.numeric' =>
                'Total tagihan harus berupa angka.',
            'total_amount.min' =>
                'Total tagihan tidak boleh kurang dari 0.',

            'amount.numeric' =>
                'Jumlah pembayaran harus berupa angka.',
            'amount.min' =>
                'Jumlah pembayaran tidak boleh kurang dari 0.',

            'status.in' =>
                'Status invoice tidak valid.',

            'due_date.date' =>
                'Tanggal jatuh tempo tidak valid.',

            'description.max' =>
                'Deskripsi maksimal 255 karakter.',
        ];
    }
}