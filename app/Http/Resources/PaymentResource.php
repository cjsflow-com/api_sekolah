<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'invoice_id' => $this->invoice_id,

            'invoice' => $this->whenLoaded(
                'invoice'
            ),

            'reference_number' => $this->reference_number,

            'payment_date' => $this->payment_date?->format(
                'Y-m-d'
            ),

            'amount' => $this->amount,

            'payment_method' => $this->payment_method,

            'reference_no' => $this->reference_no,

            'received_by' => $this->whenLoaded(
                'receivedBy'
            ),

            'description' => $this->description,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}