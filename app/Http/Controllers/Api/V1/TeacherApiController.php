<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeacherApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $teachers = Teacher::where('is_active', true)
            ->when($request->query('search'), function ($q, $term) {
                $q->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('employee_id', 'like', "%{$term}%");
            })
            ->orderBy('first_name')
            ->paginate(min((int) $request->query('per_page', 20), 100))
            ->withQueryString();

        return TeacherResource::collection($teachers);
    }

    public function show(Teacher $teacher): TeacherResource
    {
        return new TeacherResource($teacher);
    }
}
