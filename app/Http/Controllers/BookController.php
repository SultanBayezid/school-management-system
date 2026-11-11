<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::search($request->input('search'))
            ->withCount(['issues as active_issues_count' => fn ($q) => $q->where('status', 'issued')])
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        return view('library.books', compact('books'));
    }

    public function create()
    {
        return view('library.book-form', ['book' => new Book()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:200'],
            'author'         => ['nullable', 'string', 'max:150'],
            'isbn'           => ['nullable', 'string', 'max:30'],
            'category'       => ['nullable', 'string', 'max:60'],
            'total_copies'   => ['required', 'integer', 'min:1', 'max:10000'],
            'shelf'          => ['nullable', 'string', 'max:30'],
            'published_year' => ['nullable', 'integer', 'min:1900', 'max:'.now()->year],
        ]);

        Book::create($data + ['available_copies' => $data['total_copies']]);

        return redirect()->route('books.index')->with('success', 'Book added.');
    }

    public function edit(Book $book)
    {
        return view('library.book-form', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:200'],
            'author'         => ['nullable', 'string', 'max:150'],
            'isbn'           => ['nullable', 'string', 'max:30'],
            'category'       => ['nullable', 'string', 'max:60'],
            'total_copies'   => ['required', 'integer', 'min:1', 'max:10000'],
            'shelf'          => ['nullable', 'string', 'max:30'],
            'published_year' => ['nullable', 'integer', 'min:1900', 'max:'.now()->year],
        ]);

        $issued = $book->total_copies - $book->available_copies;
        if ($data['total_copies'] < $issued) {
            return back()->withInput()->with('error', "Total copies cannot be less than currently issued ({$issued}).");
        }

        $book->update($data + ['available_copies' => $data['total_copies'] - $issued]);

        return redirect()->route('books.index')->with('success', 'Book updated.');
    }

    public function destroy(Book $book)
    {
        if ($book->issues()->where('status', 'issued')->exists()) {
            return back()->with('error', 'Cannot delete: copies of this book are currently issued.');
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Book deleted.');
    }
}
