<x-app-layout title="Subjects">
    <x-slot name="actions">
        <a href="{{ route('subjects.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Subject
        </a>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Name</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Code</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Type</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Classes</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($subjects as $subject)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $subject->name }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $subject->code }}</td>
                        <td class="px-5 py-3">
                            <x-badge :type="$subject->type === 'core' ? 'sky' : 'violet'">{{ ucfirst($subject->type) }}</x-badge>
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $subject->school_classes_count }}</td>
                        <td class="px-5 py-3 text-right space-x-2">
                            <a href="{{ route('subjects.edit', $subject) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state title="No subjects" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">{{ $subjects->links() }}</div>
    </div>
</x-app-layout>
