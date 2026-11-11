<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::orderByDesc('start_date')->paginate(20);

        return view('academic-years.index', compact('years'));
    }

    public function create()
    {
        return view('academic-years.form', ['year' => new AcademicYear()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:20', 'unique:academic_years,name'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'is_current' => ['boolean'],
        ]);

        if (! empty($data['is_current'])) {
            AcademicYear::query()->update(['is_current' => false]);
        }

        AcademicYear::create($data + ['is_current' => (bool) ($data['is_current'] ?? false)]);

        return redirect()->route('academic-years.index')->with('success', 'Academic year created.');
    }

    public function edit(AcademicYear $year)
    {
        return view('academic-years.form', compact('year'));
    }

    public function update(Request $request, AcademicYear $year)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:20', 'unique:academic_years,name,'.$year->id],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'is_current' => ['boolean'],
        ]);

        if (! empty($data['is_current'])) {
            AcademicYear::whereKeyNot($year->id)->update(['is_current' => false]);
        }

        $year->update($data + ['is_current' => (bool) ($data['is_current'] ?? false)]);

        return redirect()->route('academic-years.index')->with('success', 'Academic year updated.');
    }

    public function destroy(AcademicYear $year)
    {
        if ($year->enrollments()->exists() || $year->exams()->exists() || $year->invoices()->exists()) {
            return back()->with('error', 'Cannot delete: this academic year has records.');
        }

        $year->delete();

        return redirect()->route('academic-years.index')->with('success', 'Academic year deleted.');
    }
}
