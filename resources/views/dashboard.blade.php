<x-app-layout title="Dashboard">
    <div class="space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Active Students" :value="number_format($stats['students'])" :href="route('students.index')" color="indigo" hint="Currently enrolled" />
            <x-stat-card label="Teachers" :value="number_format($stats['teachers'])" :href="route('teachers.index')" color="violet" hint="Active staff" />
            <x-stat-card label="Classes" :value="number_format($stats['classes'])" :href="route('classes.index')" color="sky" hint="School classes" />
            <x-stat-card label="Books Issued" :value="number_format($stats['books_issued'])" hint="{{ $stats['books_overdue'] }} overdue" color="amber" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Present Today" :value="number_format($stats['today_present'])" :href="route('attendance.index')" color="green" />
            <x-stat-card label="Absent Today" :value="number_format($stats['today_absent'])" :href="route('attendance.index')" color="red" />
            <x-stat-card label="Unpaid Invoices" :value="number_format($stats['unpaid_invoices'])" :href="route('invoices.index', ['status' => 'unpaid'])" color="amber" />
            <x-stat-card label="Outstanding Dues" value="BDT {{ number_format($stats['dues_amount'], 2) }}" :href="route('invoices.index')" color="red" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent notices -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Recent Notices</h2>
                    <a href="{{ route('notices.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View all</a>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($notices as $notice)
                        <a href="{{ route('notices.show', $notice) }}" class="block px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ Str::limit($notice->title, 70) }}</span>
                                <span class="text-xs text-gray-400 shrink-0">{{ $notice->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($notice->body, 100) }}</p>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-sm text-gray-500">No notices published yet.</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent payments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Recent Payments</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($recentPayments as $payment)
                        <div class="px-5 py-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $payment->invoice->student?->full_name() ?? 'Deleted student' }}</span>
                                <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">BDT {{ number_format($payment->amount, 2) }}</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">
                                {{ $payment->receipt_no }} - {{ $payment->paid_date->format('d M Y') }}
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-sm text-gray-500">No payments recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
