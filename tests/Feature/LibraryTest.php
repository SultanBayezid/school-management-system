<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Book $book;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->book = Book::create([
            'title' => 'Amar Bondhu Rashed', 'author' => 'Zafar Iqbal',
            'total_copies' => 2, 'available_copies' => 2,
        ]);
        $this->student = Student::factory()->create();
    }

    public function test_admin_can_add_a_book(): void
    {
        $response = $this->actingAs($this->admin)->post('/books', [
            'title' => 'Physics Today',
            'author' => 'S. Khan',
            'total_copies' => 3,
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', ['title' => 'Physics Today', 'available_copies' => 3]);
    }

    public function test_issuing_a_book_decrements_availability(): void
    {
        $response = $this->actingAs($this->admin)->post('/book-issues', [
            'book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'due_date' => now()->addDays(14)->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertSame(1, $this->book->fresh()->available_copies);
        $this->assertDatabaseHas('book_issues', [
            'book_id' => $this->book->id, 'student_id' => $this->student->id, 'status' => 'issued',
        ]);
    }

    public function test_book_without_available_copies_cannot_be_issued(): void
    {
        $this->book->update(['available_copies' => 0]);

        $response = $this->actingAs($this->admin)->post('/book-issues', [
            'book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'due_date' => now()->addDays(14)->toDateString(),
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(0, BookIssue::count());
    }

    public function test_returning_a_book_restores_availability(): void
    {
        $issue = BookIssue::create([
            'book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issue_date' => today()->subDays(7)->toDateString(),
            'due_date' => today()->toDateString(),
            'status' => 'issued',
        ]);
        $this->book->decrement('available_copies');

        $this->actingAs($this->admin)->patch("/book-issues/{$issue->id}/return", ['fine' => 0]);

        $this->assertSame(2, $this->book->fresh()->available_copies);
        $this->assertDatabaseHas('book_issues', ['id' => $issue->id, 'status' => 'returned']);
    }
}
