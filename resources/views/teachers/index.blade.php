<x-app-layout title="Teachers">
    <x-slot name="actions">
        @role('admin')
            <a href="{{ route('teachers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Teacher
            </a>
        @endrole
    </x-slot>

    <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, employee ID or phone..."
                   class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" />
            <button class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Search</button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Employee ID</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Name</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Phone</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Qualification</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($teachers as $teacher)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $teacher->employee_id }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('teachers.show', $teacher) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">{{ $teacher->full_name() }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $teacher->phone ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $teacher->qualification ?? '-' }}</td>
                        <td class="px-5 py-3">
                            @if ($teacher->is_active)
                                <x-badge type="green">Active</x-badge>
                            @else
                                <x-badge type="gray">Inactive</x-badge>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('teachers.show', $teacher) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">View</a>
                            @can('update', $teacher)
                                <a href="{{ route('teachers.edit', $teacher) }}" class="text-gray-600 dark:text-gray-400 hover:underline">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state title="No teachers found" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">{{ $teachers->links() }}</div>
    </div>
</x-app-layout>
