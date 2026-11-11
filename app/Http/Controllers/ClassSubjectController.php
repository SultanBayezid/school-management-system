<?php

namespace App\Http\Controllers;

use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ClassSubjectController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id'      => ['required', 'exists:subjects,id', 'unique:class_subject,subject_id,NULL,id,school_class_id,'.$request->input('school_class_id')],
            'teacher_id'      => ['nullable', 'exists:teachers,id'],
        ]);

        ClassSubject::create($data);

        return back()->with('success', 'Subject assigned to class.');
    }

    public function update(Request $request, ClassSubject $classSubject)
    {
        $data = $request->validate([
            'teacher_id' => ['nullable', 'exists:teachers,id'],
        ]);

        $classSubject->update($data);

        return back()->with('success', 'Subject teacher updated.');
    }

    public function destroy(ClassSubject $classSubject)
    {
        $classSubject->delete();

        return back()->with('success', 'Subject removed from class.');
    }
}
