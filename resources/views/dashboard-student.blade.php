<x-app-layout title="Dashboard">
    @if (! $student)
        <x-empty-state title="No student profile" description="Your account is not linked to a student profile yet. Please contact the school office." />
    @else
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Welcome, {{ $student->full_name() }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Admission No: {{ $student->admission_no }}
                            @if ($student->currentEnrollment)
                                - {{ $student->currentEnrollment->schoolClass?->name }}
                                @if ($student->currentEnrollment->section)
                                    ({{ $student->currentEnrollment->section->name }})
                                @endif
                                - Roll {{ $student->currentEnrollment->roll_no }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card label="Attendance This Month" value="{{ $attendanceSummary['rate'] }}%" color="green" hint="{{ $attendanceSummary['recorded'] }} days recorded" />
                <x-stat-card label="Days Present" :value="$attendanceSummary['counts']['present'] + $attendanceSummary['counts']['late']" color="indigo" hint="Including {{ $attendanceSummary['counts']['late'] }} late" />
                <x-stat-card label="Days Absent" :value="$attendanceSummary['counts']['absent']" color="red" hint="{{ $attendanceSummary['counts']['leave'] }} on leave" />
                <x-stat-card label="Unpaid Invoices" :value="$invoices->where('status', '!=', 'paid')->count()" color="amber" :href="route('invoices.index')" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-900 dark:text-gray-100">My Recent Invoices</h2>
                        <a href="{{ route('invoices.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View all</a>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($invoices as $invoice)
                            <a href="{{ route('invoices.show', $invoice) }}" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $invoice->feeType?->name }} {{ $invoice->month ? "({$invoice->month})" : '' }}</div>
                                    <div class="text-xs text-gray-400">{{ $invoice->invoice_no }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">BDT {{ number_format($invoice->netAmount(), 2) }}</div>
                                    <x-badge :type="match($invoice->status) { 'paid' => 'green', 'partial' => 'amber', default => 'red' }">{{ ucfirst($invoice->status) }}</x-badge>
                                </div>
                            </a>
                        @empty
                            <div class="px-5 py-8 text-sm text-gray-500">No invoices yet.</div>
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
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ Str::limit($notice->title, 50) }}</span>
                                    <span class="text-xs text-gray-400 shrink-0">{{ $notice->created_at->diffForHumans() }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="px-5 py-8 text-sm text-gray-500">No notices yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
