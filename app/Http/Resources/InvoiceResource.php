<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'invoice_no'   => $this->invoice_no,
            'student'      => $this->whenLoaded('student', fn () => $this->student?->full_name()),
            'admission_no' => $this->whenLoaded('student', fn () => $this->student?->admission_no),
            'class'        => $this->whenLoaded('schoolClass', fn () => $this->schoolClass?->name),
            'fee_type'     => $this->whenLoaded('feeType', fn () => $this->feeType?->name),
            'month'        => $this->month,
            'amount'       => (float) $this->amount,
            'discount'     => (float) $this->discount,
            'fine'         => (float) $this->fine,
            'net_amount'   => $this->netAmount(),
            'paid_amount'  => $this->paidAmount(),
            'due_amount'   => $this->dueAmount(),
            'status'       => $this->status,
            'due_date'     => $this->due_date?->toDateString(),
            'issued_date'  => $this->issued_date?->toDateString(),
            'payments'     => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}
