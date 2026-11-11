<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Exam;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\Mark;
use App\Models\Notice;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperationsSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::current();
        $classes = SchoolClass::with('sections')->orderBy('level')->get();

        // ---- Attendance (last 10 school days, Friday skipped) --------------
        $dates = collect();
        for ($i = 0; $i < 20 && $dates->count() < 10; $i++) {
            $date = today()->subDays($i);
            if ($date->dayOfWeek !== 5) { // 5 = Friday, weekend in Bangladesh
                $dates->push($date->toDateString());
            }
        }

        foreach ($classes as $class) {
            foreach ($class->enrollments()->where('academic_year_id', $year->id)->get() as $enrollment) {
                foreach ($dates as $date) {
                    $status = match (true) {
                        rand(1, 100) <= 85 => 'present',
                        rand(1, 100) <= 60 => 'absent',
                        rand(1, 100) <= 60 => 'late',
                        default => 'leave',
                    };

                    Attendance::firstOrCreate(
                        ['student_id' => $enrollment->student_id, 'attendance_date' => $date],
                        [
                            'school_class_id'  => $class->id,
                            'section_id'       => $enrollment->section_id,
                            'academic_year_id' => $year->id,
                            'status'           => $status,
                        ],
                    );
                }
            }
        }

        // ---- Exams + marks (first two classes) -----------------------------
        $examNames = ['First Terminal Examination', 'Half Yearly Examination'];

        foreach ($classes->take(2) as $classIndex => $class) {
            foreach ($examNames as $examIndex => $examName) {
                $exam = Exam::firstOrCreate(
                    [
                        'name'             => $examName,
                        'academic_year_id' => $year->id,
                        'school_class_id'  => $class->id,
                    ],
                    [
                        'start_date'   => now()->subWeeks(8 - $examIndex * 6)->toDateString(),
                        'end_date'     => now()->subWeeks(7 - $examIndex * 6)->toDateString(),
                        'is_published' => true,
                    ],
                );

                foreach ($class->classSubjects as $classSubject) {
                    foreach ($class->enrollments()->where('academic_year_id', $year->id)->get() as $enrollment) {
                        $score = rand(30, 98);

                        Mark::firstOrCreate(
                            [
                                'exam_id'    => $exam->id,
                                'student_id' => $enrollment->student_id,
                                'subject_id' => $classSubject->subject_id,
                            ],
                            [
                                'marks_obtained' => $score,
                                'max_marks'      => 100,
                            ],
                        );
                    }
                }
            }
        }

        // ---- Fees ------------------------------------------------------------
        $tuition = FeeType::firstOrCreate(
            ['name' => 'Tuition Fee'],
            ['amount' => 2500.00, 'frequency' => 'monthly', 'is_active' => true],
        );
        $examFee = FeeType::firstOrCreate(
            ['name' => 'Examination Fee'],
            ['amount' => 800.00, 'frequency' => 'one_time', 'is_active' => true],
        );

        $month = now()->format('Y-m');
        $invoiceCounter = 1;

        foreach ($classes as $class) {
            foreach ($class->enrollments()->where('academic_year_id', $year->id)->with('student')->get() as $enrollment) {
                // Tuition invoice for the current month.
                $invoice = Invoice::firstOrCreate(
                    [
                        'student_id'  => $enrollment->student_id,
                        'fee_type_id' => $tuition->id,
                        'month'       => $month,
                    ],
                    [
                        'invoice_no'       => sprintf('INV-%s-%06d', now()->format('Y'), $invoiceCounter++),
                        'school_class_id'  => $class->id,
                        'academic_year_id' => $year->id,
                        'amount'           => $tuition->amount,
                        'due_date'         => now()->endOfMonth()->toDateString(),
                        'issued_date'      => now()->startOfMonth()->toDateString(),
                        'status'           => 'unpaid',
                    ],
                );

                // 60% of students pay in full, 10% partially.
                $roll = rand(1, 100);
                if ($roll <= 60) {
                    $invoice->payments()->create([
                        'amount'      => $invoice->amount,
                        'paid_date'   => now()->subDays(rand(0, 10))->toDateString(),
                        'method'      => collect(['cash', 'bkash', 'bank'])->random(),
                        'receipt_no'  => sprintf('RCP-%s-%06d', now()->format('Y'), $invoiceCounter),
                        'received_by' => 1,
                    ]);
                    $invoice->update(['status' => 'paid']);
                } elseif ($roll <= 70) {
                    $invoice->payments()->create([
                        'amount'      => 1000,
                        'paid_date'   => now()->subDays(rand(0, 10))->toDateString(),
                        'method'      => 'bkash',
                        'receipt_no'  => sprintf('RCP-%s-%06d', now()->format('Y'), $invoiceCounter),
                        'received_by' => 1,
                    ]);
                    $invoice->update(['status' => 'partial']);
                }
            }
        }

        // ---- Notices ---------------------------------------------------------
        $notices = [
            ['title' => 'Annual Sports Day', 'body' => "Our annual sports day will be held on the school playground next month. All students must report by 8:00 AM in proper sports uniform. Parents are warmly invited to attend.", 'audience' => 'all'],
            ['title' => 'Parent-Teacher Meeting', 'body' => "A parent-teacher meeting will be held on Friday at 10:00 AM in the respective classrooms. Please collect your child's progress report from the class teacher.", 'audience' => 'parents'],
            ['title' => 'Staff Meeting', 'body' => "All teaching staff are requested to attend the monthly coordination meeting in the conference room at 2:30 PM.", 'audience' => 'teachers'],
            ['title' => 'Half Yearly Exam Routine Published', 'body' => "The half yearly examination routine has been published. Students should check the notice board and prepare accordingly. Exams start from the first week of next month.", 'audience' => 'students'],
        ];

        foreach ($notices as $notice) {
            Notice::firstOrCreate(
                ['title' => $notice['title']],
                $notice + ['published_by' => 1, 'is_active' => true, 'publish_at' => now()->subDays(rand(1, 15))],
            );
        }

        // ---- Library -----------------------------------------------------------
        $books = [
            ['title' => 'Amar Bondhu Rashed', 'author' => 'Muhammad Zafar Iqbal', 'category' => 'Fiction', 'total_copies' => 5],
            ['title' => 'Banglapedia: Children\'s Edition', 'author' => 'Asiatic Society', 'category' => 'Reference', 'total_copies' => 3],
            ['title' => 'Matir Tore', 'author' => 'Humayun Kabir', 'category' => 'Poetry', 'total_copies' => 2],
            ['title' => 'Elementary Mathematics Workbook', 'author' => 'NCTB', 'category' => 'Textbook', 'total_copies' => 10],
            ['title' => 'Physics for Young Scientists', 'author' => 'S. H. Khan', 'category' => 'Science', 'total_copies' => 4],
            ['title' => 'The Blue Marble Atlas', 'author' => 'World Publications', 'category' => 'Reference', 'total_copies' => 2],
            ['title' => 'Rupashi Bangla', 'author' => 'Jibanananda Das', 'category' => 'Poetry', 'total_copies' => 3],
            ['title' => 'Computer Basics for Kids', 'author' => 'Tanvir Ahmed', 'category' => 'ICT', 'total_copies' => 6],
        ];

        foreach ($books as $book) {
            Book::firstOrCreate(
                ['title' => $book['title']],
                $book + [
                    'available_copies' => $book['total_copies'],
                    'shelf'            => 'S-'.rand(1, 12),
                    'published_year'   => rand(2005, 2024),
                ],
            );
        }

        // Issue a few books.
        $students = \App\Models\Student::inRandomOrder()->take(6)->get();
        $bookModels = Book::inRandomOrder()->take(6)->get();

        foreach ($students as $index => $student) {
            $book = $bookModels[$index] ?? null;
            if (! $book || $book->available_copies < 1) {
                continue;
            }

            BookIssue::firstOrCreate(
                [
                    'book_id'    => $book->id,
                    'student_id' => $student->id,
                    'issue_date' => today()->subDays(7)->toDateString(),
                ],
                [
                    'due_date'   => today()->addDays(7)->toDateString(),
                    'status'     => 'issued',
                    'issued_by'  => 1,
                ],
            );

            $book->decrement('available_copies');
        }
    }
}
