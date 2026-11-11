<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BookIssue;
use App\Models\Invoice;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'students'        => Student::active()->count(),
            'teachers'        => Teacher::where('is_active', true)->count(),
            'classes'         => SchoolClass::count(),
            'present_today'   => Attendance::onDate(today())->status(Attendance::STATUS_PRESENT)->count(),
            'absent_today'    => Attendance::onDate(today())->status(Attendance::STATUS_ABSENT)->count(),
            'unpaid_invoices' => Invoice::whereIn('status', ['unpaid', 'partial'])->count(),
            'books_issued'    => BookIssue::where('status', 'issued')->count(),
            'active_notices'  => Notice::where('is_active', true)->count(),
        ]);
    }
}
