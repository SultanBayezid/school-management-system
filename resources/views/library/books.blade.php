<x-app-layout title="Library - Books">
    @role('admin')
        <x-slot name="actions">
            <a href="{{ route('books.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Add Book</a>
        </x-slot>
    @endrole

    <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, author, ISBN or category..." class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" />
            <button class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Search</button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Title</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Author</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Category</th>
                    <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Available</th>
                    <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Issued</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($books as $book)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $book->title }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $book->author ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $book->category ?? '-' }}</td>
                        <td class="px-5 py-3 text-center">
                            <x-badge :type="$book->available_copies > 0 ? 'green' : 'red'">{{ $book->available_copies }} / {{ $book->total_copies }}</x-badge>
                        </td>
                        <td class="px-5 py-3 text-center text-gray-600 dark:text-gray-400">{{ $book->active_issues_count }}</td>
                        <td class="px-5 py-3 text-right space-x-2">
                            @role('admin')
                                <a href="{{ route('books.edit', $book) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                            @endrole
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state title="No books" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">{{ $books->links() }}</div>
    </div>
</x-app-layout>
