<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\BookIssue;
use App\Models\Invoice;
use App\Models\Notice;
use App\Models\Student;
use App\Models\Teacher;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $year = AcademicYear::current();

        if ($user->isStudent()) {
            return $this->studentDashboard($user, $year);
        }

        if ($user->isParent()) {
            return $this->parentDashboard($user, $year);
        }

        // Admin and teacher dashboard - all numbers come from live data.
        $stats = [
            'students'       => Student::active()->count(),
            'teachers'       => Teacher::where('is_active', true)->count(),
            'classes'        => \App\Models\SchoolClass::count(),
            'today_present'  => Attendance::onDate(today())->status(Attendance::STATUS_PRESENT)->count(),
            'today_absent'   => Attendance::onDate(today())->status(Attendance::STATUS_ABSENT)->count(),
            'unpaid_invoices' => Invoice::whereIn('status', ['unpaid', 'partial'])->count(),
            'dues_amount'    => (float) Invoice::whereIn('status', ['unpaid', 'partial'])->get()->sum(fn ($i) => $i->dueAmount()),
            'books_issued'   => BookIssue::where('status', 'issued')->count(),
            'books_overdue'  => BookIssue::where('status', 'issued')->whereDate('due_date', '<', today())->count(),
        ];

        $notices = Notice::visibleTo($user)->latest()->take(5)->get();
        $recentPayments = \App\Models\Payment::with(['invoice.student'])
            ->latest('paid_date')->take(5)->get();

        return view('dashboard', compact('stats', 'notices', 'recentPayments', 'year'));
    }

    private function studentDashboard($user, $year)
    {
        $student = $user->student()->with('currentEnrollment.schoolClass', 'currentEnrollment.section')->first();

        if (! $student) {
            return view('dashboard-student', ['student' => null, 'year' => $year, 'notices' => Notice::visibleTo($user)->latest()->take(5)->get()]);
        }

        $attendanceSummary = app(\App\Services\AttendanceService::class)
            ->monthlySummary($student->id, now()->format('Y-m'));

        $invoices = $student->invoices()
            ->with('feeType')
            ->latest()
            ->take(5)
            ->get();

        $notices = Notice::visibleTo($user)->latest()->take(5)->get();

        return view('dashboard-student', compact('student', 'attendanceSummary', 'invoices', 'notices', 'year'));
    }

    private function parentDashboard($user, $year)
    {
        $children = $user->children()->with('currentEnrollment.schoolClass', 'currentEnrollment.section')->get();

        $unpaid = Invoice::whereIn('student_id', $children->pluck('id'))
            ->whereIn('status', ['unpaid', 'partial'])
            ->with('student', 'feeType')
            ->get();

        $notices = Notice::visibleTo($user)->latest()->take(5)->get();

        return view('dashboard-parent', compact('children', 'unpaid', 'notices', 'year'));
    }
}
