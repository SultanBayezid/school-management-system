<x-app-layout title="Users">
    <x-slot name="actions">
        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Add User</a>
    </x-slot>

    <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" />
            <select name="role" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                <option value="">All Roles</option>
                @foreach (\App\Models\User::ROLES as $value => $label)
                    <option value="{{ $value }}" @selected(request('role') == $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Filter</button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Name</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Email</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Role</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($users as $u)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $u->name }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $u->email }}</td>
                        <td class="px-5 py-3"><x-badge :type="match($u->role) { 'admin' => 'violet', 'teacher' => 'sky', 'student' => 'green', default => 'amber' }">{{ $u->roleLabel() }}</x-badge></td>
                        <td class="px-5 py-3">
                            @if ($u->is_active)
                                <x-badge type="green">Active</x-badge>
                            @else
                                <x-badge type="gray">Disabled</x-badge>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('users.edit', $u) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state title="No users found" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">{{ $users->links() }}</div>
    </div>
</x-app-layout>
