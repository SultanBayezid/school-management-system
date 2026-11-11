<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->input('search'), function ($q, $term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        })
            ->when($request->input('role'), fn ($q, $r) => $q->where('role', $r))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['user' => new User()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(array_keys(User::ROLES))],
            'phone'    => ['nullable', 'string', 'max:40'],
            'is_active' => ['boolean'],
        ]);

        User::create($data + ['is_active' => (bool) ($data['is_active'] ?? true)]);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:150'],
            'email'     => ['required', 'email', 'max:150', 'unique:users,email,'.$user->id],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'      => ['required', Rule::in(array_keys(User::ROLES))],
            'phone'     => ['nullable', 'string', 'max:40'],
            'is_active' => ['boolean'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Prevent locking yourself out.
        if ($user->id === $request->user()->id && (empty($data['is_active']) || $data['role'] !== 'admin')) {
            return back()->with('error', 'You cannot deactivate or demote your own account.');
        }

        $user->update($data + ['is_active' => (bool) ($data['is_active'] ?? false)]);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
