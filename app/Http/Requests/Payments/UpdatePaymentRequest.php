<?php

namespace App\Http\Requests\Payments;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Payment $payment */
        $payment = $this->route('payment');

        return [
            'invoice_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:invoices,id',
            ],

            'reference_number' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'payments',
                    'reference_number'
                )->ignore($payment),
            ],

            'payment_date' => [
                'nullable',
                'date',
            ],

            'amount' => [
                'sometimes',
                'required',
                'numeric',
                'gt:0',
            ],

            'payment_method' => [
                'sometimes',
                'required',
                Rule::in([
                    Payment::METHOD_CASH,
                    Payment::METHOD_TRANSFER,
                    Payment::METHOD_E_WALLET,
                ]),
            ],

            'reference_no' => [
                'nullable',
                'string',
                'max:255',
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
            'invoice_id.required' =>
                'Invoice wajib dipilih.',

            'invoice_id.exists' =>
                'Invoice tidak ditemukan.',

            'reference_number.required' =>
                'Nomor pembayaran wajib diisi.',

            'reference_number.unique' =>
                'Nomor pembayaran sudah digunakan.',

            'payment_date.date' =>
                'Tanggal pembayaran tidak valid.',

            'amount.required' =>
                'Jumlah pembayaran wajib diisi.',

            'amount.numeric' =>
                'Jumlah pembayaran harus berupa angka.',

            'amount.gt' =>
                'Jumlah pembayaran harus lebih dari 0.',

            'payment_method.required' =>
                'Metode pembayaran wajib dipilih.',

            'payment_method.in' =>
                'Metode pembayaran tidak valid.',

            'reference_no.max' =>
                'Nomor referensi maksimal 255 karakter.',

            'description.max' =>
                'Deskripsi maksimal 255 karakter.',
        ];
    }
}