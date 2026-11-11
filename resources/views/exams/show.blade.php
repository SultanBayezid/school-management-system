<x-app-layout :title="$exam->name">
    <x-slot name="actions">
        <a href="{{ route('exams.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
        <a href="{{ route('exams.marks-entry', $exam) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Enter Marks</a>
        @role('admin')
            <a href="{{ route('exams.edit', $exam) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Edit</a>
        @endrole
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $exam->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $exam->schoolClass?->name }} · {{ $exam->academicYear?->name }}
                        @if ($exam->start_date || $exam->end_date)
                            · {{ $exam->start_date?->format('d M') ?? '?' }} to {{ $exam->end_date?->format('d M Y') ?? '?' }}
                        @endif
                    </p>
                </div>
                @if ($exam->is_published)
                    <x-badge type="green">Published</x-badge>
                @else
                    <x-badge type="amber">Draft - results hidden from students</x-badge>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Subject-wise Statistics</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Subject</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Entries</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Average</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Highest</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Lowest</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($stats as $row)
                        <tr>
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $row->subject?->name }}</td>
                            <td class="px-5 py-3 text-center text-gray-600 dark:text-gray-400">{{ $row->entries }}</td>
                            <td class="px-5 py-3 text-center text-gray-600 dark:text-gray-400">{{ number_format($row->avg_marks, 2) }}</td>
                            <td class="px-5 py-3 text-center text-green-600 dark:text-green-400 font-medium">{{ number_format($row->best, 2) }}</td>
                            <td class="px-5 py-3 text-center text-red-600 dark:text-red-400 font-medium">{{ number_format($row->worst, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state title="No marks recorded" description="Use 'Enter Marks' to record results for this exam." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Report Cards</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Open a student profile from the Students list, or enter marks to see per-student links.</p>
        </div>
    </div>
</x-app-layout>
