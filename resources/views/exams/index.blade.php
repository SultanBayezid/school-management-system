<x-app-layout title="Exams & Results">
    <x-slot name="actions">
        @role('admin')
            <a href="{{ route('exams.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Add Exam</a>
        @endrole
    </x-slot>

    <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex gap-3">
            <select name="class_id" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                <option value="">All Classes</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Filter</button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Exam</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Class</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Year</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Dates</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Marks</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($exams as $exam)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                            <a href="{{ route('exams.show', $exam) }}" class="hover:text-indigo-600">{{ $exam->name }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $exam->schoolClass?->name }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $exam->academicYear?->name }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">
                            {{ $exam->start_date?->format('d M') ?? '?' }} - {{ $exam->end_date?->format('d M Y') ?? '?' }}
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $exam->marks_count }}</td>
                        <td class="px-5 py-3">
                            @if ($exam->is_published)
                                <x-badge type="green">Published</x-badge>
                            @else
                                <x-badge type="amber">Draft</x-badge>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('exams.marks-entry', $exam) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Marks</a>
                            <a href="{{ route('exams.show', $exam) }}" class="text-gray-600 dark:text-gray-400 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-empty-state title="No exams" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">{{ $exams->links() }}</div>
    </div>
</x-app-layout>
