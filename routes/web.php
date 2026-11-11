<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookIssueController;
use App\Http\Controllers\ClassSubjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\FeeTypeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ------------------------------------------------ viewing (all roles) --
    Route::middleware('role:admin,teacher,student,parent')->group(function () {
        Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');
        Route::get('/notices/{notice}', [NoticeController::class, 'show'])->name('notices.show');
    });

    // --------------------------------------- students (admin/teacher see all)
    Route::middleware('role:admin,teacher,student,parent')->group(function () {
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    });

    Route::middleware('role:admin,teacher,student,parent')->group(function () {
        Route::get('/students/{student}', [StudentController::class, 'show'])
            ->middleware('can:view,student')->name('students.show');
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    });

    // ------------------------------------------------------------- teachers --
    Route::middleware('role:admin,teacher')->group(function () {
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
    });
    Route::middleware('role:admin,teacher')->group(function () {
        Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');
    });
    Route::middleware('role:admin')->group(function () {
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    });

    // ------------------------------------------------- academic structure ----
    Route::middleware('role:admin')->group(function () {
        Route::get('/academic-years/create', [AcademicYearController::class, 'create'])->name('academic-years.create');
        Route::resource('academic-years', AcademicYearController::class)->except(['show', 'create']);

        Route::get('/classes/create', [SchoolClassController::class, 'create'])->name('classes.create');
        Route::resource('classes', SchoolClassController::class)->except(['create'])->parameters([
            'classes' => 'class',
        ]);
        Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');
        Route::put('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');

        Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
        Route::resource('subjects', SubjectController::class)->except(['show', 'create']);
        Route::post('/class-subjects', [ClassSubjectController::class, 'store'])->name('class-subjects.store');
        Route::put('/class-subjects/{classSubject}', [ClassSubjectController::class, 'update'])->name('class-subjects.update');
        Route::delete('/class-subjects/{classSubject}', [ClassSubjectController::class, 'destroy'])->name('class-subjects.destroy');

        Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
        Route::get('/enrollments/create', [EnrollmentController::class, 'create'])->name('enrollments.create');
        Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
        Route::put('/enrollments/{enrollment}', [EnrollmentController::class, 'update'])->name('enrollments.update');
        Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    });

    // ------------------------------------------------------------ attendance --
    Route::middleware('role:admin,teacher')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    });

    // ----------------------------------------------------------------- exams --
    Route::middleware('role:admin,teacher')->group(function () {
        Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');
    });
    Route::middleware('role:admin,teacher')->group(function () {
        Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
        Route::get('/exams/{exam}/marks', [ExamController::class, 'marksEntry'])->name('exams.marks-entry');
        Route::post('/exams/{exam}/marks', [ExamController::class, 'marksStore'])->name('exams.marks-store');
        Route::get('/exams/{exam}/report-card/{student}', [ExamController::class, 'reportCard'])->name('exams.report-card');
    });
    Route::middleware('role:admin')->group(function () {
        Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
        Route::get('/exams/{exam}/edit', [ExamController::class, 'edit'])->name('exams.edit');
        Route::put('/exams/{exam}', [ExamController::class, 'update'])->name('exams.update');
        Route::delete('/exams/{exam}', [ExamController::class, 'destroy'])->name('exams.destroy');
    });

    // ------------------------------------------------------------------ fees --
    Route::middleware('role:admin,teacher,student,parent')->group(function () {
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
            ->middleware('can:view,invoice')->name('invoices.show');
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/invoices-create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::post('/invoices/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::resource('fee-types', FeeTypeController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    // --------------------------------------------------------------- notices --
    Route::middleware('role:admin,teacher')->group(function () {
        Route::get('/notices-create', [NoticeController::class, 'create'])->name('notices.create');
        Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');
        Route::get('/notices/{notice}/edit', [NoticeController::class, 'edit'])->name('notices.edit');
        Route::put('/notices/{notice}', [NoticeController::class, 'update'])->name('notices.update');
        Route::delete('/notices/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy');
    });

    // ------------------------------------------------------------- timetable --
    Route::middleware('role:admin,teacher,student,parent')->group(function () {
        Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');
    });
    Route::middleware('role:admin')->group(function () {
        Route::post('/timetable', [TimetableController::class, 'store'])->name('timetable.store');
        Route::delete('/timetable/{entry}', [TimetableController::class, 'destroy'])->name('timetable.destroy');
    });

    // --------------------------------------------------------------- library --
    Route::middleware('role:admin,teacher')->group(function () {
        Route::get('/books', [BookController::class, 'index'])->name('books.index');
        Route::get('/book-issues', [BookIssueController::class, 'index'])->name('book-issues.index');
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('/books', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
        Route::get('/book-issues/create', [BookIssueController::class, 'create'])->name('book-issues.create');
        Route::post('/book-issues', [BookIssueController::class, 'store'])->name('book-issues.store');
        Route::patch('/book-issues/{bookIssue}/return', [BookIssueController::class, 'return'])
            ->name('book-issues.return');
    });

    // ---------------------------------------------------- settings and users --
    Route::middleware('role:admin')->group(function () {
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/profile.php';
