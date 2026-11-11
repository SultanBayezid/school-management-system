<x-app-layout title="Library - Issued Books">
    @role('admin')
        <x-slot name="actions">
            <a href="{{ route('book-issues.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Issue Book</a>
        </x-slot>
    @endrole

    <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex gap-3">
            <select name="status" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                <option value="">All Statuses</option>
                @foreach (['issued', 'returned', 'lost'] as $s)
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
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Book</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Student</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Issued</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Due</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($issues as $issue)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ Str::limit($issue->book?->title, 35) }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('students.show', $issue->student) }}" class="text-gray-700 dark:text-gray-300 hover:text-indigo-600">{{ $issue->student?->full_name() }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $issue->issue_date->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $issue->due_date->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            @if ($issue->status === 'issued' && $issue->isOverdue())
                                <x-badge type="red">Overdue</x-badge>
                            @else
                                <x-badge :type="match($issue->status) { 'returned' => 'green', 'lost' => 'red', default => 'amber' }">{{ ucfirst($issue->status) }}</x-badge>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            @role('admin')
                                @if ($issue->status === 'issued')
                                    <form method="POST" action="{{ route('book-issues.return', $issue) }}" class="inline-flex items-center gap-2"
                                          onsubmit="this.querySelector('.fine-input').classList.toggle('hidden'); return false;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="fine" min="0" step="0.01" placeholder="Fine"
                                               class="fine-input hidden w-24 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-xs" />
                                        <button class="text-green-600 dark:text-green-400 hover:underline text-sm" type="button"
                                                onclick="event.preventDefault(); var f=this.closest('form'); f.onsubmit=null; f.requestSubmit();">Return</button>
                                    </form>
                                @endif
                            @endrole
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state title="No book issues" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">{{ $issues->links() }}</div>
    </div>
</x-app-layout>
