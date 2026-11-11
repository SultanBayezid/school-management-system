<x-app-layout title="Classes & Sections">
    <x-slot name="actions">
        <a href="{{ route('classes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Class
        </a>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Class</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Level</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Sections</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Students (current year)</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($classes as $class)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3">
                            <a href="{{ route('classes.show', $class) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">{{ $class->name }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $class->level }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $class->sections_count }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $class->active_enrollments_count }}</td>
                        <td class="px-5 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('classes.show', $class) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Manage</a>
                            <a href="{{ route('classes.edit', $class) }}" class="text-gray-600 dark:text-gray-400 hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state title="No classes" description="Create your first school class to get started." /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">{{ $classes->links() }}</div>
    </div>
</x-app-layout>
