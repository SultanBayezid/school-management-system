<x-app-layout title="Report Card">
    <x-slot name="actions">
        <a href="javascript:history.back()" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
        <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Print</button>
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 print:shadow-none print:border-0">
        {{-- Header --}}
        <div class="text-center border-b-2 border-gray-800 dark:border-gray-200 pb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\SchoolSetting::find(1)?->school_name ?? 'School' }}</h1>
            <p class="text-sm text-gray-500">{{ \App\Models\SchoolSetting::find(1)?->school_address }}</p>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mt-3">Academic Report Card</h2>
            <p class="text-sm text-gray-500">{{ $exam->name }} · {{ $exam->academicYear?->name }}</p>
        </div>

        {{-- Student info --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-5 text-sm">
            <div><span class="text-gray-500">Name:</span> <span class="font-medium text-gray-900 dark:text-gray-100">{{ $student->full_name() }}</span></div>
            <div><span class="text-gray-500">Admission No:</span> <span class="font-medium">{{ $student->admission_no }}</span></div>
            <div><span class="text-gray-500">Class:</span> <span class="font-medium">{{ $student->currentEnrollment?->schoolClass?->name ?? '-' }}</span></div>
            <div><span class="text-gray-500">Roll:</span> <span class="font-medium">{{ $student->currentEnrollment?->roll_no ?? '-' }}</span></div>
        </div>

        {{-- Marks table --}}
        <table class="min-w-full border border-gray-300 dark:border-gray-600 text-sm">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700 text-left">
                    <th class="border border-gray-300 dark:border-gray-600 px-3 py-2 font-semibold">Subject</th>
                    <th class="border border-gray-300 dark:border-gray-600 px-3 py-2 font-semibold text-center">Full Marks</th>
                    <th class="border border-gray-300 dark:border-gray-600 px-3 py-2 font-semibold text-center">Obtained</th>
                    <th class="border border-gray-300 dark:border-gray-600 px-3 py-2 font-semibold text-center">Percentage</th>
                    <th class="border border-gray-300 dark:border-gray-600 px-3 py-2 font-semibold text-center">Grade</th>
                    <th class="border border-gray-300 dark:border-gray-600 px-3 py-2 font-semibold text-center">Point</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($report['rows'] as $row)
                    <tr>
                        <td class="border border-gray-300 dark:border-gray-600 px-3 py-2">{{ $row['subject'] }}</td>
                        <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center">{{ number_format($row['max'], 0) }}</td>
                        <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center font-medium">{{ number_format($row['obtained'], 2) }}</td>
                        <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center">{{ $row['percentage'] }}%</td>
                        <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center font-semibold">{{ $row['grade'] ?? '-' }}</td>
                        <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center">{{ number_format($row['point'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="border border-gray-300 px-3 py-6 text-center text-gray-500">No marks recorded.</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-semibold">
                <tr>
                    <td class="border border-gray-300 dark:border-gray-600 px-3 py-2">Total</td>
                    <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center">{{ number_format($report['total_max'], 0) }}</td>
                    <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center">{{ number_format($report['total_obtained'], 2) }}</td>
                    <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center">{{ $report['percentage'] }}%</td>
                    <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center">{{ $report['grade'] ?? '-' }}</td>
                    <td class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center">GPA {{ number_format($report['grade_point'], 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Summary --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 text-sm">
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center">
                <div class="text-xs text-gray-500">Average</div>
                <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($report['average'], 2) }}</div>
            </div>
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center">
                <div class="text-xs text-gray-500">Overall Grade</div>
                <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $report['grade'] ?? '-' }}</div>
            </div>
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center">
                <div class="text-xs text-gray-500">Class Position</div>
                <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $position > 0 ? $position : '-' }}</div>
            </div>
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center">
                <div class="text-xs text-gray-500">Failed Subjects</div>
                <div class="text-lg font-bold {{ $report['failed_subjects'] > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $report['failed_subjects'] }}</div>
            </div>
        </div>

        {{-- Grade bands legend --}}
        <div class="mt-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Grading Scale</h3>
            <div class="flex flex-wrap gap-2 text-xs">
                @foreach (\App\Models\GradeBand::orderByDesc('min_percent')->get() as $band)
                    <span class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-gray-600 dark:text-gray-400">
                        {{ $band->grade }}: {{ rtrim(rtrim($band->min_percent, '0'), '.') }}-{{ rtrim(rtrim($band->max_percent, '0'), '.') }}% ({{ $band->grade_point }})
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
