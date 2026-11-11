<x-app-layout title="Dashboard">
    <div class="space-y-6">
        @if ($children->isEmpty())
            <x-empty-state title="No children linked" description="Your parent account is not linked to any student yet. Please contact the school office." />
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($children as $child)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $child->full_name() }}</h3>
                            @if ($child->is_active)
                                <x-badge type="green">Active</x-badge>
                            @else
                                <x-badge type="gray">Inactive</x-badge>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $child->admission_no }}
                            @if ($child->currentEnrollment)
                                - {{ $child->currentEnrollment->schoolClass?->name }}
                            @endif
                        </p>
                        <a href="{{ route('students.show', $child) }}" class="mt-3 inline-flex text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View details</a>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Outstanding Fees</h2>
                <a href="{{ route('invoices.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View all invoices</a>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($unpaid as $invoice)
                    <a href="{{ route('invoices.show', $invoice) }}" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $invoice->student?->full_name() }} - {{ $invoice->feeType?->name }}</div>
                            <div class="text-xs text-gray-400">{{ $invoice->invoice_no }} {{ $invoice->month ? "({$invoice->month})" : '' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-rose-600 dark:text-rose-400">BDT {{ number_format($invoice->dueAmount(), 2) }} due</div>
                            <x-badge :type="match($invoice->status) { 'paid' => 'green', 'partial' => 'amber', default => 'red' }">{{ ucfirst($invoice->status) }}</x-badge>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-8 text-sm text-gray-500">All fees are paid. Thank you!</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Notices</h2>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($notices as $notice)
                    <a href="{{ route('notices.show', $notice) }}" class="block px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ Str::limit($notice->title, 70) }}</span>
                        <span class="text-xs text-gray-400 ms-2">{{ $notice->created_at->diffForHumans() }}</span>
                    </a>
                @empty
                    <div class="px-5 py-8 text-sm text-gray-500">No notices yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
