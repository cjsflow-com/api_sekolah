<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,

            'student_id' => $this->student_id,
            'student' => $this->whenLoaded(
                'student'
            ),

            'fee_type_id' => $this->fee_type_id,
            'fee_type' => $this->whenLoaded(
                'feeType'
            ),

            'semester_id' => $this->semester_id,
            'semester' => $this->whenLoaded(
                'semester'
            ),

            'month' => $this->month,
            'year' => $this->year,

            'total_amount' => $this->total_amount,
            'amount' => $this->amount,
            'remaining_amount' => $this->remaining_amount,

            'status' => $this->status,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'description' => $this->description,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}