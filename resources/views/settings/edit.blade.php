<x-app-layout title="School Settings">
    <form method="POST" action="{{ route('settings.update') }}" class="max-w-2xl">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <x-input-label for="school_name" value="School Name *" />
                <x-text-input id="school_name" name="school_name" type="text" :value="old('school_name', $settings->school_name)" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('school_name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="school_address" value="Address" />
                <textarea id="school_address" name="school_address" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('school_address', $settings->school_address) }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="phone" value="Phone" />
                    <x-text-input id="phone" name="phone" type="text" :value="old('phone', $settings->phone)" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" :value="old('email', $settings->email)" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="website" value="Website" />
                    <x-text-input id="website" name="website" type="url" :value="old('website', $settings->website)" placeholder="https://example.com" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="principal_name" value="Principal Name" />
                    <x-text-input id="principal_name" name="principal_name" type="text" :value="old('principal_name', $settings->principal_name)" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="established_year" value="Established Year" />
                    <x-text-input id="established_year" name="established_year" type="number" min="1800" max="{{ now()->year }}" :value="old('established_year', $settings->established_year)" class="mt-1 block w-full" />
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Save Settings</button>
        </div>
    </form>
</x-app-layout>
