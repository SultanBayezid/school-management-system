<x-app-layout :title="$year->exists ? 'Edit Academic Year' : 'Add Academic Year'">
    <x-slot name="actions">
        <a href="{{ route('academic-years.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
    </x-slot>

    <form method="POST" action="{{ $year->exists ? route('academic-years.update', $year) : route('academic-years.store') }}" class="max-w-2xl">
        @csrf
        @if ($year->exists)
            @method('PUT')
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <x-input-label for="name" value="Year Name *" />
                <x-text-input id="name" name="name" type="text" :value="old('name', $year->name)" placeholder="e.g. 2026" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="start_date" value="Start Date *" />
                    <x-text-input id="start_date" name="start_date" type="date" :value="old('start_date', $year->start_date?->format('Y-m-d'))" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="end_date" value="End Date *" />
                    <x-text-input id="end_date" name="end_date" type="date" :value="old('end_date', $year->end_date?->format('Y-m-d'))" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <input type="checkbox" name="is_current" value="1" @checked(old('is_current', $year->is_current)) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                Set as current academic year
            </label>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                {{ $year->exists ? 'Update' : 'Create' }}
            </button>
        </div>
    </form>
</x-app-layout>
