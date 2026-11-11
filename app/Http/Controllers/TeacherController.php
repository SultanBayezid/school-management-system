<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $teachers = Teacher::with('user')
            ->when($request->input('search'), function ($q, $term) {
                $q->where(function ($qq) use ($term) {
                    $qq->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('employee_id', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('teachers.form', ['teacher' => new Teacher()]);
    }

    public function store(StoreTeacherRequest $request)
    {
        $data = $request->validated();

        // Optionally create a login account for the teacher.
        if ($request->boolean('create_login') && $request->input('login_email')) {
            $credentials = $request->validate([
                'login_email'    => ['required', 'email', 'unique:users,email'],
                'login_password' => ['required', 'string', 'min:8'],
            ]);

            $user = User::create([
                'name'     => $data['first_name'].' '.$data['last_name'],
                'email'    => $credentials['login_email'],
                'password' => $credentials['login_password'],
                'role'     => User::ROLE_TEACHER,
                'phone'    => $data['phone'] ?? null,
            ]);

            $data['user_id'] = $user->id;
        }

        Teacher::create($data);

        return redirect()->route('teachers.index')->with('success', 'Teacher added.');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['sections', 'classSubjects.subject', 'classSubjects.schoolClass', 'timetableEntries.subject']);

        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        return view('teachers.form', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $teacher->update($request->validated());

        return redirect()->route('teachers.show', $teacher)->with('success', 'Teacher updated.');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->classSubjects()->exists() || $teacher->timetableEntries()->exists()) {
            return back()->with('error', 'Cannot delete: teacher is assigned to subjects or timetable.');
        }

        DB::transaction(function () use ($teacher) {
            $teacher->user?->delete();
            $teacher->delete();
        });

        return redirect()->route('teachers.index')->with('success', 'Teacher deleted.');
    }
}
