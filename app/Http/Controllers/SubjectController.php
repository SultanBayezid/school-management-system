<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount('schoolClasses')->orderBy('name')->paginate(30);

        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('subjects.form', ['subject' => new Subject()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'code'        => ['required', 'string', 'max:20', 'unique:subjects,code'],
            'type'        => ['required', 'in:core,elective'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Subject::create($data);

        return redirect()->route('subjects.index')->with('success', 'Subject created.');
    }

    public function edit(Subject $subject)
    {
        return view('subjects.form', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'code'        => ['required', 'string', 'max:20', 'unique:subjects,code,'.$subject->id],
            'type'        => ['required', 'in:core,elective'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $subject->update($data);

        return redirect()->route('subjects.index')->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->marks()->exists() || $subject->schoolClasses()->exists()) {
            return back()->with('error', 'Cannot delete: subject is assigned to classes or has marks.');
        }

        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Subject deleted.');
    }
}
