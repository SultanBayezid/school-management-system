<x-app-layout title="Enrollments">
    <x-slot name="actions">
        <a href="{{ route('enrollments.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">New Enrollment</a>
    </x-slot>

    <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <select name="year_id" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                <option value="">All Years</option>
                @foreach ($years as $y)
                    <option value="{{ $y->id }}" @selected(request('year_id') == $y->id)>{{ $y->name }}</option>
                @endforeach
            </select>
            <select name="class_id" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                <option value="">All Classes</option>
                @foreach ($classes as $c)
                    <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                <option value="">All Statuses</option>
                @foreach (['active', 'completed', 'dropped'] as $s)
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
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Roll</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Student</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Class</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Section</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Year</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($enrollments as $enrollment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3 text-gray-500">{{ $enrollment->roll_no ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('students.show', $enrollment->student) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600">{{ $enrollment->student?->full_name() }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $enrollment->schoolClass?->name }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $enrollment->section?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $enrollment->academicYear?->name }}</td>
                        <td class="px-5 py-3">
                            <x-badge :type="match($enrollment->status) { 'active' => 'green', 'completed' => 'sky', default => 'gray' }">{{ ucfirst($enrollment->status) }}</x-badge>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state title="No enrollments" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">{{ $enrollments->links() }}</div>
    </div>
</x-app-layout>
