<x-app-layout title="New Enrollment">
    <x-slot name="actions">
        <a href="{{ route('enrollments.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
    </x-slot>

    <form method="POST" action="{{ route('enrollments.store') }}" class="max-w-3xl">
        @csrf
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="student_id" value="Student *" />
                    <select id="student_id" name="student_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                        <option value="">Select student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->full_name() }} ({{ $student->admission_no }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="academic_year_id" value="Academic Year *" />
                    <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}" @selected($year->is_current)>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="school_class_id" value="Class *" />
                    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                        <option value="">Select class</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('school_class_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="section_id" value="Section" />
                    <select id="section_id" name="section_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        <option value="">None</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="roll_no" value="Roll No" />
                    <x-text-input id="roll_no" name="roll_no" type="number" min="1" :value="old('roll_no')" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('roll_no')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        <option value="active" @selected(old('status') === 'active')>Active</option>
                        <option value="completed">Completed</option>
                        <option value="dropped">Dropped</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Enroll Student</button>
        </div>
    </form>
</x-app-layout>
