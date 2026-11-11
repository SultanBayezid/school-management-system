<x-app-layout title="Academic Years">
    <x-slot name="actions">
        <a href="{{ route('academic-years.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Year
        </a>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Year</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Start</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">End</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($years as $year)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $year->name }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $year->start_date->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $year->end_date->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            @if ($year->is_current)
                                <x-badge type="green">Current</x-badge>
                            @else
                                <x-badge type="gray">Inactive</x-badge>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('academic-years.edit', $year) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state title="No academic years" description="Add an academic year to begin enrolling students." /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">{{ $years->links() }}</div>
    </div>
</x-app-layout>
