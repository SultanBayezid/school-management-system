<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'exists:invoices,id'],
            'amount'     => ['required', 'numeric', 'min:0.01'],
            'paid_date'  => ['required', 'date', 'before_or_equal:today'],
            'method'     => ['required', 'in:'.implode(',', \App\Models\Payment::METHODS)],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $invoice = \App\Models\Invoice::find($this->input('invoice_id'));
            $amount  = (float) $this->input('amount', 0);

            if ($invoice && $amount > $invoice->dueAmount() + 0.001) {
                $validator->errors()->add(
                    'amount',
                    'Payment exceeds the due amount of '.$invoice->dueAmount().'.'
                );
            }
        });
    }
}
