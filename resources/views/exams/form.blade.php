<x-app-layout :title="$exam->exists ? 'Edit Exam' : 'Add Exam'">
    <x-slot name="actions">
        <a href="{{ route('exams.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
    </x-slot>

    <form method="POST" action="{{ $exam->exists ? route('exams.update', $exam) : route('exams.store') }}" class="max-w-2xl">
        @csrf
        @if ($exam->exists)
            @method('PUT')
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <x-input-label for="name" value="Exam Name *" />
                <x-text-input id="name" name="name" type="text" :value="old('name', $exam->name)" placeholder="e.g. First Terminal Examination" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="academic_year_id" value="Academic Year *" />
                    <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}" @selected(old('academic_year_id', $exam->academic_year_id ?? ($year->is_current ? $year->id : null)) == $year->id)>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="school_class_id" value="Class *" />
                    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                        <option value="">Select class</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected(old('school_class_id', $exam->school_class_id) == $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="start_date" value="Start Date" />
                    <x-text-input id="start_date" name="start_date" type="date" :value="old('start_date', $exam->start_date?->format('Y-m-d'))" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="end_date" value="End Date" />
                    <x-text-input id="end_date" name="end_date" type="date" :value="old('end_date', $exam->end_date?->format('Y-m-d'))" class="mt-1 block w-full" />
                </div>
            </div>
            <div>
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('description', $exam->description) }}</textarea>
            </div>
            @if ($exam->exists)
                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $exam->is_published)) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                    Published (visible to students)
                </label>
            @endif
        </div>

        <div class="mt-6">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                {{ $exam->exists ? 'Update Exam' : 'Create Exam' }}
            </button>
        </div>
    </form>
</x-app-layout>
