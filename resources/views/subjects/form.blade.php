<x-app-layout :title="$subject->exists ? 'Edit Subject' : 'Add Subject'">
    <x-slot name="actions">
        <a href="{{ route('subjects.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back to list</a>
    </x-slot>

    <form method="POST" action="{{ $subject->exists ? route('subjects.update', $subject) : route('subjects.store') }}" class="max-w-2xl">
        @csrf
        @if ($subject->exists)
            @method('PUT')
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="name" value="Subject Name *" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', $subject->name)" placeholder="e.g. Mathematics" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="code" value="Code *" />
                    <x-text-input id="code" name="code" type="text" :value="old('code', $subject->code)" placeholder="e.g. MATH101" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
            </div>
            <div>
                <x-input-label for="type" value="Type *" />
                <select id="type" name="type" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option value="core" @selected(old('type', $subject->type) === 'core')>Core</option>
                    <option value="elective" @selected(old('type', $subject->type) === 'elective')>Elective</option>
                </select>
            </div>
            <div>
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('description', $subject->description) }}</textarea>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                {{ $subject->exists ? 'Update Subject' : 'Add Subject' }}
            </button>
        </div>
    </form>
</x-app-layout>
