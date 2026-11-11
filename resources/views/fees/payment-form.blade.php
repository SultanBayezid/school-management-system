<x-app-layout title="Record Payment">
    <x-slot name="actions">
        <a href="{{ route('invoices.show', $invoice) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back to invoice</a>
    </x-slot>

    <div class="max-w-2xl space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $invoice->invoice_no }}</h2>
            <p class="text-sm text-gray-500">{{ $invoice->student?->full_name() }} · {{ $invoice->feeType?->name }} {{ $invoice->month ? "({$invoice->month})" : '' }}</p>
            <div class="grid grid-cols-3 gap-4 mt-4 text-sm">
                <div><div class="text-gray-500">Net Payable</div><div class="font-semibold">BDT {{ number_format($invoice->netAmount(), 2) }}</div></div>
                <div><div class="text-gray-500">Already Paid</div><div class="font-semibold text-green-600">BDT {{ number_format($invoice->paidAmount(), 2) }}</div></div>
                <div><div class="text-gray-500">Due</div><div class="font-semibold text-red-600">BDT {{ number_format($invoice->dueAmount(), 2) }}</div></div>
            </div>
        </div>

        <form method="POST" action="{{ route('payments.store') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            @csrf
            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="amount" value="Amount (BDT) *" />
                    <x-text-input id="amount" name="amount" type="number" min="0.01" step="0.01" :value="old('amount', $invoice->dueAmount())" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="paid_date" value="Payment Date *" />
                    <x-text-input id="paid_date" name="paid_date" type="date" :value="old('paid_date', today()->toDateString())" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('paid_date')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="method" value="Method *" />
                    <select id="method" name="method" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @foreach (\App\Models\Payment::METHODS as $method)
                            <option value="{{ $method }}" @selected(old('method') === $method)>{{ ucfirst($method) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="notes" value="Notes" />
                    <x-text-input id="notes" name="notes" type="text" :value="old('notes')" class="mt-1 block w-full" />
                </div>
            </div>

            <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">Save Payment</button>
        </form>
    </div>
</x-app-layout>
