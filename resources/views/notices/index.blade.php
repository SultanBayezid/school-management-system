<x-app-layout title="Notices">
    @if (auth()->user()->isAdmin() || auth()->user()->isTeacher())
        <x-slot name="actions">
            <a href="{{ route('notices.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">New Notice</a>
        </x-slot>
    @endif

    <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search notices..." class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" />
            <button class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Search</button>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse ($notices as $notice)
            <a href="{{ route('notices.show', $notice) }}" class="block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">{{ $notice->title }}</h2>
                    <div class="flex items-center gap-2">
                        <x-badge :type="match($notice->audience) { 'all' => 'sky', 'teachers' => 'violet', 'students' => 'green', default => 'amber' }">
                            {{ ucfirst($notice->audience) }}
                        </x-badge>
                        @unless (auth()->user()->isAdmin())
                            @if ($notice->publish_at)
                                <span class="text-xs text-gray-400">{{ $notice->publish_at->diffForHumans() }}</span>
                            @else
                                <span class="text-xs text-gray-400">{{ $notice->created_at->diffForHumans() }}</span>
                            @endif
                        @endunless
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ Str::limit($notice->body, 160) }}</p>
            </a>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <x-empty-state title="No notices" description="Published notices will appear here." />
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $notices->links() }}</div>
</x-app-layout>
