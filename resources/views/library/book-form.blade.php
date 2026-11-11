<x-app-layout :title="$book->exists ? 'Edit Book' : 'Add Book'">
    <x-slot name="actions">
        <a href="{{ route('books.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
    </x-slot>

    <form method="POST" action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}" class="max-w-2xl">
        @csrf
        @if ($book->exists)
            @method('PUT')
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <x-input-label for="title" value="Title *" />
                <x-text-input id="title" name="title" type="text" :value="old('title', $book->title)" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="author" value="Author" />
                    <x-text-input id="author" name="author" type="text" :value="old('author', $book->author)" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="isbn" value="ISBN" />
                    <x-text-input id="isbn" name="isbn" type="text" :value="old('isbn', $book->isbn)" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="category" value="Category" />
                    <x-text-input id="category" name="category" type="text" :value="old('category', $book->category)" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="total_copies" value="Total Copies *" />
                    <x-text-input id="total_copies" name="total_copies" type="number" min="1" :value="old('total_copies', $book->total_copies ?? 1)" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="shelf" value="Shelf" />
                    <x-text-input id="shelf" name="shelf" type="text" :value="old('shelf', $book->shelf)" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="published_year" value="Published Year" />
                    <x-text-input id="published_year" name="published_year" type="number" min="1900" max="{{ now()->year }}" :value="old('published_year', $book->published_year)" class="mt-1 block w-full" />
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                {{ $book->exists ? 'Update Book' : 'Add Book' }}
            </button>
        </div>
    </form>
</x-app-layout>
