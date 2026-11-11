<x-app-layout title="Issue Book">
    <x-slot name="actions">
        <a href="{{ route('book-issues.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
    </x-slot>

    <form method="POST" action="{{ route('book-issues.store') }}" class="max-w-2xl">
        @csrf
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <x-input-label for="book_id" value="Book *" />
                <select id="book_id" name="book_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                    <option value="">Select book</option>
                    @foreach ($books as $book)
                        <option value="{{ $book->id }}">{{ $book->title }} ({{ $book->available_copies }} available)</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('book_id')" class="mt-2" />
            </div>
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
                <x-input-label for="due_date" value="Due Date *" />
                <x-text-input id="due_date" name="due_date" type="date" :value="old('due_date', now()->addDays(14)->toDateString())" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Issue Book</button>
        </div>
    </form>
</x-app-layout>
