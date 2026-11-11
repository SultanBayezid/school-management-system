<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StudentApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->user()->can('viewAny', Student::class)
            ?: abort(403, 'You may not browse students.');

        $students = Student::with('currentEnrollment.schoolClass', 'currentEnrollment.section')
            ->active()
            ->search($request->query('search'))
            ->when($request->query('class_id'), function ($q, $classId) {
                $q->whereHas('currentEnrollment', fn ($e) => $e->where('school_class_id', $classId));
            })
            ->orderBy('first_name')
            ->paginate(min((int) $request->query('per_page', 20), 100))
            ->withQueryString();

        return StudentResource::collection($students);
    }

    public function show(Student $student): StudentResource
    {
        $request = request();
        $request->user()->can('view', $student) ?: abort(403);

        $student->load('currentEnrollment.schoolClass', 'currentEnrollment.section');

        return new StudentResource($student);
    }
}
