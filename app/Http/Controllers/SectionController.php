<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'name'            => ['required', 'string', 'max:30'],
            'capacity'        => ['required', 'integer', 'min:1', 'max:200'],
            'teacher_id'      => ['nullable', 'exists:teachers,id'],
        ]);

        Section::create($data);

        return back()->with('success', 'Section created.');
    }

    public function update(Request $request, Section $section)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:30'],
            'capacity'   => ['required', 'integer', 'min:1', 'max:200'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
        ]);

        $section->update($data);

        return back()->with('success', 'Section updated.');
    }

    public function destroy(Section $section)
    {
        if ($section->enrollments()->exists()) {
            return back()->with('error', 'Cannot delete: section has enrolled students.');
        }

        $section->delete();

        return back()->with('success', 'Section deleted.');
    }
}
