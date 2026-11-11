<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'receipt_no' => $this->receipt_no,
            'amount'     => (float) $this->amount,
            'paid_date'  => $this->paid_date?->toDateString(),
            'method'     => $this->method,
        ];
    }
}
