<x-app-layout :title="$notice->title">
    <x-slot name="actions">
        <a href="{{ route('notices.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
        @if (auth()->user()->isAdmin() || (auth()->user()->isTeacher() && $notice->published_by === auth()->id()))
            <a href="{{ route('notices.edit', $notice) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Edit</a>
        @endif
    </x-slot>

    <article class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 dark:border-gray-700 pb-4">
            <x-badge :type="match($notice->audience) { 'all' => 'sky', 'teachers' => 'violet', 'students' => 'green', default => 'amber' }">{{ ucfirst($notice->audience) }}</x-badge>
            <span class="text-xs text-gray-400">
                Published {{ ($notice->publish_at ?? $notice->created_at)->format('d M Y, h:i A') }}
                @if ($notice->publisher)
                    by {{ $notice->publisher->name }}
                @endif
            </span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-5">{{ $notice->title }}</h1>
        <div class="mt-4 text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $notice->body }}</div>
    </article>
</x-app-layout>
