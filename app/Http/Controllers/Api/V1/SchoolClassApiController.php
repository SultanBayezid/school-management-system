<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SchoolClassResource;
use App\Models\SchoolClass;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SchoolClassApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $classes = SchoolClass::with(['sections' => fn ($q) => $q->withCount('activeEnrollments')])
            ->with('subjects')
            ->orderBy('level')
            ->get();

        return SchoolClassResource::collection($classes);
    }

    public function show(SchoolClass $class): SchoolClassResource
    {
        $class->load(['sections' => fn ($q) => $q->withCount('activeEnrollments')], 'subjects');

        return new SchoolClassResource($class);
    }
}
