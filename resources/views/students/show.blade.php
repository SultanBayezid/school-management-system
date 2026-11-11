<x-app-layout :title="$student->full_name()">
    <x-slot name="actions">
        <a href="{{ route('students.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
        @can('update', $student)
            <a href="{{ route('students.edit', $student) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Edit</a>
        @endcan
    </x-slot>

    <div class="space-y-6">
        <!-- Profile card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-300 flex items-center justify-center text-2xl font-bold">
                        {{ strtoupper(substr($student->first_name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $student->full_name() }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->admission_no }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if ($student->is_active)
                        <x-badge type="green">Active</x-badge>
                    @else
                        <x-badge type="gray">Inactive</x-badge>
                    @endif
                    @if ($student->currentEnrollment)
                        <x-badge type="sky">{{ $student->currentEnrollment->schoolClass?->name }} {{ $student->currentEnrollment->section ? '· '.$student->currentEnrollment->section->name : '' }} · Roll {{ $student->currentEnrollment->roll_no }}</x-badge>
                    @endif
                </div>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 text-sm">
                <div><dt class="text-gray-500 dark:text-gray-400">Date of Birth</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-200">{{ $student->date_of_birth?->format('d M Y') ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Gender</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-200">{{ ucfirst($student->gender ?? '-') }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Blood Group</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-200">{{ $student->blood_group ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Phone</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-200">{{ $student->phone ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Guardian</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-200">{{ $student->guardian_name ?? '-' }} ({{ $student->guardian_relation ?? '-' }})</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Guardian Phone</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-200">{{ $student->guardian_phone ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Admitted On</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-200">{{ $student->admission_date?->format('d M Y') ?? '-' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-gray-500 dark:text-gray-400">Address</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-200">{{ $student->address ?? '-' }}</dd></div>
            </dl>
        </div>

        <!-- Attendance summary -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <x-stat-card label="Attendance Rate" value="{{ $attendance['rate'] }}%" color="green" hint="This month" />
            <x-stat-card label="Present" :value="$attendance['counts']['present']" color="indigo" />
            <x-stat-card label="Absent" :value="$attendance['counts']['absent']" color="red" />
            <x-stat-card label="Late" :value="$attendance['counts']['late']" color="amber" />
            <x-stat-card label="Leave" :value="$attendance['counts']['leave']" color="violet" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Invoices -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Recent Invoices</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($invoices as $invoice)
                        <div class="flex items-center justify-between px-5 py-3 text-sm">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $invoice->feeType?->name }} {{ $invoice->month ? "({$invoice->month})" : '' }}</div>
                                <div class="text-xs text-gray-400">{{ $invoice->invoice_no }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">BDT {{ number_format($invoice->netAmount(), 2) }}</div>
                                <x-badge :type="match($invoice->status) { 'paid' => 'green', 'partial' => 'amber', default => 'red' }">{{ ucfirst($invoice->status) }}</x-badge>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-gray-500">No invoices.</div>
                    @endforelse
                </div>
            </div>

            <!-- Library -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Library History</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($bookIssues as $issue)
                        <div class="flex items-center justify-between px-5 py-3 text-sm">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ Str::limit($issue->book?->title, 40) }}</div>
                                <div class="text-xs text-gray-400">Issued {{ $issue->issue_date->format('d M Y') }} · Due {{ $issue->due_date->format('d M Y') }}</div>
                            </div>
                            <x-badge :type="match($issue->status) { 'returned' => 'green', 'lost' => 'red', default => $issue->isOverdue() ? 'red' : 'amber' }">
                                {{ $issue->isOverdue() && $issue->status === 'issued' ? 'Overdue' : ucfirst($issue->status) }}
                            </x-badge>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-gray-500">No books issued.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
