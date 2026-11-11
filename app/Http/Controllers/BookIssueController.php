<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookIssueController extends Controller
{
    public function index(Request $request)
    {
        $issues = BookIssue::with(['book', 'student'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest('issue_date')
            ->paginate(20)
            ->withQueryString();

        return view('library.issues', compact('issues'));
    }

    public function create()
    {
        $books = Book::where('available_copies', '>', 0)->orderBy('title')->get();
        $students = Student::active()->orderBy('first_name')->get();

        return view('library.issue-form', compact('books', 'students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'book_id'    => ['required', 'exists:books,id'],
            'student_id' => ['required', 'exists:students,id'],
            'due_date'   => ['required', 'date', 'after:today'],
        ]);

        $book = Book::lockForUpdate()->findOrFail($data['book_id']);

        if ($book->available_copies < 1) {
            return back()->withInput()->with('error', 'No copies of this book are currently available.');
        }

        DB::transaction(function () use ($book, $data, $request) {
            BookIssue::create([
                'book_id'    => $data['book_id'],
                'student_id' => $data['student_id'],
                'issue_date' => now()->toDateString(),
                'due_date'   => $data['due_date'],
                'status'     => BookIssue::STATUS_ISSUED,
                'issued_by'  => $request->user()->id,
            ]);

            $book->decrement('available_copies');
        });

        return redirect()->route('book-issues.index')->with('success', 'Book issued.');
    }

    public function return(Request $request, BookIssue $bookIssue)
    {
        if ($bookIssue->status !== BookIssue::STATUS_ISSUED) {
            return back()->with('error', 'This book has already been returned.');
        }

        $data = $request->validate([
            'fine' => ['nullable', 'numeric', 'min:0', 'max:100000'],
        ]);

        DB::transaction(function () use ($bookIssue, $data) {
            $bookIssue->update([
                'return_date' => now()->toDateString(),
                'status'      => BookIssue::STATUS_RETURNED,
                'fine'        => $data['fine'] ?? 0,
            ]);

            $bookIssue->book->increment('available_copies');
        });

        return back()->with('success', 'Book returned.');
    }
}
