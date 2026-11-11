<x-app-layout title="Attendance Report">
    <x-slot name="actions">
        <a href="{{ route('attendance.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Daily Entry</a>
    </x-slot>

    <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex items-end gap-3">
            <div>
                <x-input-label for="month" value="Month" />
                <input type="month" id="month" name="month" value="{{ $month }}" class="mt-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" />
            </div>
            <button class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Show</button>
        </form>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @foreach (\App\Models\Attendance::STATUSES as $status)
            <x-stat-card label="{{ ucfirst($status) }} (month)" :value="number_format($summary->get($status, 0))"
                         :color="match($status) { 'present' => 'green', 'absent' => 'red', 'late' => 'amber', default => 'violet' }" />
        @endforeach
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Admission No</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Student</th>
                    <th class="px-5 py-3 text-center font-semibold text-green-600">Present</th>
                    <th class="px-5 py-3 text-center font-semibold text-red-600">Absent</th>
                    <th class="px-5 py-3 text-center font-semibold text-amber-600">Late</th>
                    <th class="px-5 py-3 text-center font-semibold text-violet-600">Leave</th>
                    <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Days</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($rows as $studentId => $group)
                    @php $first = $group->first(); $tot = $group->sum('c'); @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $first->admission_no }}</td>
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $first->first_name }} {{ $first->last_name }}</td>
                        @foreach (['present', 'absent', 'late', 'leave'] as $status)
                            <td class="px-5 py-3 text-center text-gray-600 dark:text-gray-400">
                                {{ $group->firstWhere('status', $status)?->c ?? 0 }}
                            </td>
                        @endforeach
                        <td class="px-5 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">{{ $tot }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-empty-state title="No attendance data" description="No records for {{ $month }}." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
