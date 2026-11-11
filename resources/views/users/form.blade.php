<x-app-layout :title="$user->exists ? 'Edit User' : 'Add User'">
    <x-slot name="actions">
        <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
    </x-slot>

    <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" class="max-w-2xl">
        @csrf
        @if ($user->exists)
            @method('PUT')
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <x-input-label for="name" value="Name *" />
                <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="email" value="Email *" />
                <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password" value="{{ $user->exists ? 'New Password (leave blank to keep current)' : 'Password *' }}" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" @unless($user->exists) required @endunless />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password_confirmation" value="Confirm Password" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="role" value="Role *" />
                    <select id="role" name="role" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                        @foreach (\App\Models\User::ROLES as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', $user->role ?? 'student') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="phone" value="Phone" />
                    <x-text-input id="phone" name="phone" type="text" :value="old('phone', $user->phone)" class="mt-1 block w-full" />
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->exists ? $user->is_active : true)) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                Account active
            </label>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                {{ $user->exists ? 'Update User' : 'Create User' }}
            </button>
        </div>
    </form>
</x-app-layout>
