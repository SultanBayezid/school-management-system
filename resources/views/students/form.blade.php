<x-app-layout :title="$student->exists ? 'Edit Student' : 'Add Student'">
    <x-slot name="actions">
        <a href="{{ route('students.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back to list</a>
    </x-slot>

    <form method="POST" action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}" class="space-y-6 max-w-4xl">
        @csrf
        @if ($student->exists)
            @method('PUT')
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Student Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="admission_no" value="Admission No *" />
                    <x-text-input id="admission_no" name="admission_no" type="text" :value="old('admission_no', $student->admission_no)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('admission_no')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="admission_date" value="Admission Date" />
                    <x-text-input id="admission_date" name="admission_date" type="date" :value="old('admission_date', $student->admission_date?->format('Y-m-d'))" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('admission_date')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="first_name" value="First Name *" />
                    <x-text-input id="first_name" name="first_name" type="text" :value="old('first_name', $student->first_name)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="last_name" value="Last Name *" />
                    <x-text-input id="last_name" name="last_name" type="text" :value="old('last_name', $student->last_name)" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="gender" value="Gender" />
                    <select id="gender" name="gender" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @foreach (['' => 'Select', 'male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('gender', $student->gender) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="date_of_birth" value="Date of Birth" />
                    <x-text-input id="date_of_birth" name="date_of_birth" type="date" :value="old('date_of_birth', $student->date_of_birth?->format('Y-m-d'))" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="blood_group" value="Blood Group" />
                    <x-text-input id="blood_group" name="blood_group" type="text" :value="old('blood_group', $student->blood_group)" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('blood_group')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="phone" value="Phone" />
                    <x-text-input id="phone" name="phone" type="text" :value="old('phone', $student->phone)" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="address" value="Address" />
                    <textarea id="address" name="address" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('address', $student->address) }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Guardian Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="guardian_name" value="Guardian Name" />
                    <x-text-input id="guardian_name" name="guardian_name" type="text" :value="old('guardian_name', $student->guardian_name)" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('guardian_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="guardian_phone" value="Guardian Phone" />
                    <x-text-input id="guardian_phone" name="guardian_phone" type="text" :value="old('guardian_phone', $student->guardian_phone)" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('guardian_phone')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="guardian_relation" value="Relation" />
                    <x-text-input id="guardian_relation" name="guardian_relation" type="text" :value="old('guardian_relation', $student->guardian_relation)" placeholder="Father / Mother / Other" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('guardian_relation')" class="mt-2" />
                </div>
            </div>
        </div>

        @if (! $student->exists)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Initial Enrollment (optional)</h2>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <x-input-label for="school_class_id" value="Class" />
                        <select id="school_class_id" name="school_class_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">None</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(old('school_class_id') == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="section_id" value="Section" />
                        <select id="section_id" name="section_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">None</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="academic_year_id" value="Academic Year *" />
                        <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                            @foreach ($years as $year)
                                <option value="{{ $year->id }}" @selected(old('academic_year_id', $year->is_current ? $year->id : null) == $year->id)>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="roll_no" value="Roll No" />
                        <x-text-input id="roll_no" name="roll_no" type="number" min="1" :value="old('roll_no')" class="mt-1 block w-full" />
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-3">Sections load after choosing a class.</p>
            </div>
        @endif

        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                {{ $student->exists ? 'Update Student' : 'Add Student' }}
            </button>
            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $student->exists ? $student->is_active : true)) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                Active
            </label>
        </div>
    </form>

    {{-- Dynamic sections per class --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sectionsByClass = @json($classes->mapWithKeys(fn ($c) => [$c->id => $c->sections->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])]));
            const classSelect = document.getElementById('school_class_id');
            const sectionSelect = document.getElementById('section_id');
            if (! classSelect || ! sectionSelect) return;
            classSelect.addEventListener('change', function () {
                sectionSelect.innerHTML = '<option value="">None</option>';
                (sectionsByClass[this.value] || []).forEach(function (s) {
                    const opt = document.createElement('option');
                    opt.value = s.id; opt.textContent = s.name;
                    sectionSelect.appendChild(opt);
                });
            });
        });
    </script>
</x-app-layout>
