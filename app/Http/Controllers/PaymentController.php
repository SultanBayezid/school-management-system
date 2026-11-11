<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        $invoice = Invoice::with(['student', 'feeType', 'payments'])->findOrFail($request->input('invoice_id'));

        $this->authorize('view', $invoice);

        return view('fees.payment-form', compact('invoice'));
    }

    public function store(StorePaymentRequest $request)
    {
        $invoice = Invoice::findOrFail($request->validated('invoice_id'));

        $payment = $invoice->payments()->create($request->validated() + [
            'receipt_no'  => $this->nextReceiptNo(),
            'received_by' => $request->user()->id,
        ]);

        $invoice->refreshStatus();

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Payment recorded. Receipt '.$payment->receipt_no);
    }

    private function nextReceiptNo(): string
    {
        $count = Payment::count() + 1;

        return sprintf('RCP-%s-%06d', now()->format('Y'), $count);
    }
}
