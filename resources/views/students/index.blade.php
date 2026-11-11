<x-app-layout title="Students">
    <x-slot name="actions">
        @can('create', \App\Models\Student::class)
            <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Student
            </a>
        @endcan
    </x-slot>

    <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, admission no, guardian..."
                   class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
            <select name="class_id" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                <option value="">All Classes</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-sm font-medium rounded-lg">Filter</button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Admission No</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Name</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Class</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Guardian</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Phone</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($students as $student)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $student->admission_no }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('students.show', $student) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">{{ $student->full_name() }}</a>
                            @if (! $student->is_active)
                                <x-badge type="gray">Inactive</x-badge>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                            {{ $student->currentEnrollment?->schoolClass?->name ?? '-' }}
                            @if ($student->currentEnrollment?->section)
                                <span class="text-xs text-gray-400">({{ $student->currentEnrollment->section->name }})</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $student->guardian_name ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $student->guardian_phone ?? '-' }}</td>
                        <td class="px-5 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('students.show', $student) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">View</a>
                            @can('update', $student)
                                <a href="{{ route('students.edit', $student) }}" class="text-gray-600 dark:text-gray-400 hover:underline">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state title="No students found" description="Try adjusting the search or add a new student." /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $students->links() }}
        </div>
    </div>
</x-app-layout>
