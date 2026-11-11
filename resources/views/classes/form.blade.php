<x-app-layout :title="$class->exists ? 'Edit Class' : 'Add Class'">
    <x-slot name="actions">
        <a href="{{ route('classes.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back to list</a>
    </x-slot>

    <form method="POST" action="{{ $class->exists ? route('classes.update', $class) : route('classes.store') }}" class="max-w-2xl">
        @csrf
        @if ($class->exists)
            @method('PUT')
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <x-input-label for="name" value="Class Name *" />
                <x-text-input id="name" name="name" type="text" :value="old('name', $class->name)" placeholder="e.g. Class Six" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="level" value="Level (sort order) *" />
                <x-text-input id="level" name="level" type="number" min="1" max="20" :value="old('level', $class->level ?? 1)" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('level')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('description', $class->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                {{ $class->exists ? 'Update Class' : 'Add Class' }}
            </button>
        </div>
    </form>
</x-app-layout>
