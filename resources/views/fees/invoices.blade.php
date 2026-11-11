<x-app-layout title="Invoices">
    @role('admin')
        <x-slot name="actions">
            <a href="{{ route('invoices.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">New Invoice</a>
        </x-slot>
    @endrole

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <x-stat-card label="Total Billed (filtered)" value="BDT {{ number_format($totals['billed'], 2) }}" color="sky" />
        <x-stat-card label="Collected (filtered)" value="BDT {{ number_format($totals['collected'], 2) }}" color="green" />
        <x-stat-card label="Outstanding (filtered)" value="BDT {{ number_format($totals['billed'] - $totals['collected'], 2) }}" color="red" />
    </div>

    <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoice no or student..." class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" />
            <select name="status" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                <option value="">All Statuses</option>
                @foreach (['unpaid', 'partial', 'paid'] as $s)
                    <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Filter</button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Invoice</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Student</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Fee</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Net</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Due</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($invoices as $invoice)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3">
                            <a href="{{ route('invoices.show', $invoice) }}" class="font-mono text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">{{ $invoice->invoice_no }}</a>
                            @if ($invoice->month)
                                <div class="text-[11px] text-gray-400">{{ $invoice->month }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-900 dark:text-gray-100">{{ $invoice->student?->full_name() }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $invoice->feeType?->name }}</td>
                        <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">BDT {{ number_format($invoice->netAmount(), 2) }}</td>
                        <td class="px-5 py-3 text-right font-medium {{ $invoice->dueAmount() > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">BDT {{ number_format($invoice->dueAmount(), 2) }}</td>
                        <td class="px-5 py-3">
                            <x-badge :type="match($invoice->status) { 'paid' => 'green', 'partial' => 'amber', default => 'red' }">{{ ucfirst($invoice->status) }}</x-badge>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state title="No invoices" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">{{ $invoices->links() }}</div>
    </div>
</x-app-layout>
