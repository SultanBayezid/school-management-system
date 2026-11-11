<x-app-layout :title="$class->name">
    <x-slot name="actions">
        <a href="{{ route('classes.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
        <a href="{{ route('classes.edit', $class) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Edit</a>
    </x-slot>

    <div class="space-y-6">
        <!-- Sections -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Sections</h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @forelse ($class->sections as $section)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $section->name }}</span>
                                <span class="text-xs text-gray-400">Capacity {{ $section->capacity }}</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Class teacher: {{ $section->classTeacher?->full_name() ?? 'Not assigned' }}
                            </p>
                            <form method="POST" action="{{ route('sections.destroy', $section) }}" class="mt-3" onsubmit="return confirm('Delete this section?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-600 hover:underline">Delete</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No sections yet.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('sections.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end border-t border-gray-200 dark:border-gray-700 pt-4">
                    @csrf
                    <input type="hidden" name="school_class_id" value="{{ $class->id }}">
                    <div>
                        <x-input-label for="sec_name" value="New Section Name *" />
                        <x-text-input id="sec_name" name="name" placeholder="Section C" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="sec_capacity" value="Capacity" />
                        <x-text-input id="sec_capacity" name="capacity" type="number" min="1" value="40" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="sec_teacher" value="Class Teacher" />
                        <select id="sec_teacher" name="teacher_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">None</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->full_name() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Add Section</button>
                </form>
            </div>
        </div>

        <!-- Subjects -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Subjects &amp; Teachers</h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 dark:text-gray-400">
                                <th class="pb-2 pr-4 font-medium">Subject</th>
                                <th class="pb-2 pr-4 font-medium">Code</th>
                                <th class="pb-2 pr-4 font-medium">Teacher</th>
                                <th class="pb-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($class->classSubjects as $cs)
                                <tr>
                                    <td class="py-2 pr-4 font-medium text-gray-900 dark:text-gray-100">{{ $cs->subject?->name }}</td>
                                    <td class="py-2 pr-4 text-gray-500">{{ $cs->subject?->code }}</td>
                                    <td class="py-2 pr-4">
                                        <form method="POST" action="{{ route('class-subjects.update', $cs) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <select name="teacher_id" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-xs">
                                                <option value="">Not assigned</option>
                                                @foreach ($teachers as $teacher)
                                                    <option value="{{ $teacher->id }}" @selected($cs->teacher_id === $teacher->id)>{{ $teacher->full_name() }}</option>
                                                @endforeach
                                            </select>
                                            <button class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Save</button>
                                        </form>
                                    </td>
                                    <td class="py-2 text-right">
                                        <form method="POST" action="{{ route('class-subjects.destroy', $cs) }}" onsubmit="return confirm('Remove subject from class?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs text-red-600 hover:underline">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-3 text-sm text-gray-500">No subjects assigned yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <form method="POST" action="{{ route('class-subjects.store') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end border-t border-gray-200 dark:border-gray-700 pt-4">
                    @csrf
                    <input type="hidden" name="school_class_id" value="{{ $class->id }}">
                    <div>
                        <x-input-label for="cs_subject" value="Subject *" />
                        <select id="cs_subject" name="subject_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                            <option value="">Select subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="cs_teacher" value="Teacher" />
                        <select id="cs_teacher" name="teacher_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Not assigned</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->full_name() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Assign Subject</button>
                </form>
            </div>
        </div>

        <!-- Students -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">Students (current year)</h2>
                <a href="{{ route('students.create') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Add student</a>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Roll</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Admission No</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Name</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Section</th>
                        <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="px-5 py-3 text-gray-500">{{ $enrollment->roll_no ?? '-' }}</td>
                            <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $enrollment->student?->admission_no }}</td>
                            <td class="px-5 py-3">
                                <a href="{{ route('students.show', $enrollment->student) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600">{{ $enrollment->student?->full_name() }}</a>
                            </td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $enrollment->section?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('enrollments.index', ['class_id' => $class->id]) }}" class="text-xs text-gray-400 hover:underline">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-sm text-gray-500">No students enrolled in the current academic year.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">{{ $enrollments->links() }}</div>
        </div>
    </div>
</x-app-layout>
