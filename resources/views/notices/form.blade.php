<x-app-layout :title="$notice->exists ? 'Edit Notice' : 'New Notice'">
    <x-slot name="actions">
        <a href="{{ route('notices.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
    </x-slot>

    <form method="POST" action="{{ $notice->exists ? route('notices.update', $notice) : route('notices.store') }}" class="max-w-3xl">
        @csrf
        @if ($notice->exists)
            @method('PUT')
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <x-input-label for="title" value="Title *" />
                <x-text-input id="title" name="title" type="text" :value="old('title', $notice->title)" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="body" value="Body *" />
                <textarea id="body" name="body" rows="7" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>{{ old('body', $notice->body) }}</textarea>
                <x-input-error :messages="$errors->get('body')" class="mt-2" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="audience" value="Audience *" />
                    <select id="audience" name="audience" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @foreach (\App\Models\Notice::AUDIENCES as $audience)
                            <option value="{{ $audience }}" @selected(old('audience', $notice->audience ?? 'all') === $audience)>{{ ucfirst($audience) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="publish_at" value="Publish At (optional)" />
                    <x-text-input id="publish_at" name="publish_at" type="datetime-local" :value="old('publish_at', $notice->publish_at?->format('Y-m-d\TH:i'))" class="mt-1 block w-full" />
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $notice->exists ? $notice->is_active : true)) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                Active (visible to audience)
            </label>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                {{ $notice->exists ? 'Update Notice' : 'Publish Notice' }}
            </button>
        </div>
    </form>
</x-app-layout>
