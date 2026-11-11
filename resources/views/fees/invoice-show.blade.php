<x-app-layout :title="'Invoice '.$invoice->invoice_no">
    <x-slot name="actions">
        <a href="{{ route('invoices.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
        @if (auth()->user()->isAdmin() && $invoice->dueAmount() > 0)
            <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">Record Payment</a>
        @endif
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-200 dark:border-gray-700 pb-5">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\SchoolSetting::find(1)?->school_name ?? 'School' }}</h1>
                    <p class="text-sm text-gray-500">{{ \App\Models\SchoolSetting::find(1)?->school_address }}</p>
                </div>
                <div class="text-right">
                    <div class="font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $invoice->invoice_no }}</div>
                    <div class="text-xs text-gray-500">Issued {{ $invoice->issued_date->format('d M Y') }}</div>
                    @if ($invoice->due_date)
                        <div class="text-xs text-gray-500">Due {{ $invoice->due_date->format('d M Y') }}</div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-5 text-sm">
                <div>
                    <div class="text-gray-500">Student</div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $invoice->student?->full_name() }}</div>
                    <div class="text-xs text-gray-400">{{ $invoice->student?->admission_no }} · {{ $invoice->schoolClass?->name }}</div>
                </div>
                <div class="sm:text-right">
                    <div class="text-gray-500">Fee</div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $invoice->feeType?->name }} {{ $invoice->month ? "({$invoice->month})" : '' }}</div>
                    <x-badge :type="match($invoice->status) { 'paid' => 'green', 'partial' => 'amber', default => 'red' }" class="mt-1">{{ ucfirst($invoice->status) }}</x-badge>
                </div>
            </div>

            <table class="min-w-full text-sm">
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr><td class="py-2 text-gray-600 dark:text-gray-400">Amount</td><td class="py-2 text-right font-medium">BDT {{ number_format($invoice->amount, 2) }}</td></tr>
                    <tr><td class="py-2 text-gray-600 dark:text-gray-400">Discount</td><td class="py-2 text-right text-green-600 dark:text-green-400">- BDT {{ number_format($invoice->discount, 2) }}</td></tr>
                    <tr><td class="py-2 text-gray-600 dark:text-gray-400">Fine</td><td class="py-2 text-right text-red-600 dark:text-red-400">+ BDT {{ number_format($invoice->fine, 2) }}</td></tr>
                    <tr class="font-semibold"><td class="py-2 text-gray-900 dark:text-gray-100">Net Payable</td><td class="py-2 text-right text-gray-900 dark:text-gray-100">BDT {{ number_format($invoice->netAmount(), 2) }}</td></tr>
                    <tr><td class="py-2 text-gray-600 dark:text-gray-400">Paid</td><td class="py-2 text-right text-green-600 dark:text-green-400 font-medium">BDT {{ number_format($invoice->paidAmount(), 2) }}</td></tr>
                    <tr class="font-bold text-base"><td class="py-2">Due</td><td class="py-2 text-right {{ $invoice->dueAmount() > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">BDT {{ number_format($invoice->dueAmount(), 2) }}</td></tr>
                </tbody>
            </table>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Payment History</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Receipt</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Date</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Method</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Received By</th>
                        <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($invoice->payments as $payment)
                        <tr>
                            <td class="px-5 py-3 font-mono text-xs">{{ $payment->receipt_no }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $payment->paid_date->format('d M Y') }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-400 capitalize">{{ $payment->method }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $payment->receiver?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-right font-medium text-green-600 dark:text-green-400">BDT {{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-6 text-sm text-gray-500">No payments recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
