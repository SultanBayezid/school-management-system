<x-app-layout :title="$teacher->exists ? 'Edit Teacher' : 'Add Teacher'">
    <x-slot name="actions">
        <a href="{{ route('teachers.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back to list</a>
    </x-slot>

    <form method="POST" action="{{ $teacher->exists ? route('teachers.update', $teacher) : route('teachers.store') }}" class="space-y-6 max-w-3xl">
        @csrf
        @if ($teacher->exists)
            @method('PUT')
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="employee_id" value="Employee ID *" />
                    <x-text-input id="employee_id" name="employee_id" type="text" :value="old('employee_id', $teacher->employee_id)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="join_date" value="Join Date" />
                    <x-text-input id="join_date" name="join_date" type="date" :value="old('join_date', $teacher->join_date?->format('Y-m-d'))" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('join_date')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="first_name" value="First Name *" />
                    <x-text-input id="first_name" name="first_name" type="text" :value="old('first_name', $teacher->first_name)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="last_name" value="Last Name *" />
                    <x-text-input id="last_name" name="last_name" type="text" :value="old('last_name', $teacher->last_name)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="gender" value="Gender" />
                    <select id="gender" name="gender" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @foreach (['' => 'Select', 'male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('gender', $teacher->gender) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="date_of_birth" value="Date of Birth" />
                    <x-text-input id="date_of_birth" name="date_of_birth" type="date" :value="old('date_of_birth', $teacher->date_of_birth?->format('Y-m-d'))" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="phone" value="Phone" />
                    <x-text-input id="phone" name="phone" type="text" :value="old('phone', $teacher->phone)" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" :value="old('email', $teacher->email)" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="qualification" value="Qualification" />
                    <x-text-input id="qualification" name="qualification" type="text" :value="old('qualification', $teacher->qualification)" placeholder="e.g. MSc in Mathematics" class="mt-1 block w-full" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="address" value="Address" />
                    <textarea id="address" name="address" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('address', $teacher->address) }}</textarea>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $teacher->exists ? $teacher->is_active : true)) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                Active
            </label>
        </div>

        @if (! $teacher->exists)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Create Login Account (optional)</h2>
                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                    <input type="checkbox" name="create_login" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="document.getElementById('login-fields').classList.toggle('hidden', ! this.checked)" />
                    Create a user account for this teacher
                </label>
                <div id="login-fields" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="login_email" value="Login Email *" />
                        <x-text-input id="login_email" name="login_email" type="email" :value="old('login_email')" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('login_email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="login_password" value="Password *" />
                        <x-text-input id="login_password" name="login_password" type="password" :value="old('login_password')" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('login_password')" class="mt-2" />
                    </div>
                </div>
            </div>
        @endif

        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
            {{ $teacher->exists ? 'Update Teacher' : 'Add Teacher' }}
        </button>
    </form>
</x-app-layout>
