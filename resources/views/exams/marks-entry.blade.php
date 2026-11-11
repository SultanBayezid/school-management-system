<x-app-layout :title="'Marks - '.$exam->name">
    <x-slot name="actions">
        <a href="{{ route('exams.show', $exam) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back to exam</a>
    </x-slot>

    @if (! $subject)
        <x-empty-state title="No subjects assigned" description="Assign subjects to this class first (Classes &amp; Sections -> Manage)." />
    @else
        <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex flex-wrap gap-2">
            @foreach ($subjects as $s)
                <a href="{{ route('exams.marks-entry', ['exam' => $exam, 'subject_id' => $s->id]) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $subject->id === $s->id ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    {{ $s->name }}
                </a>
            @endforeach
        </div>

        <form method="POST" action="{{ route('exams.marks-store', $exam) }}">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
            <input type="hidden" name="max_marks" id="max-marks" value="{{ old('max_marks', $existing->first()->max_marks ?? 100) }}">

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-gray-100">{{ $subject->name }}</h2>
                        <p class="text-xs text-gray-500">{{ $exam->schoolClass?->name }} · {{ $students->count() }} students</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600 dark:text-gray-400">Max marks</label>
                        <input type="number" name="max_marks_display" id="max-marks-input" min="1" max="1000"
                               value="{{ old('max_marks', $existing->first()->max_marks ?? 100) }}"
                               class="w-20 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm"
                               onchange="document.getElementById('max-marks').value = this.value" />
                    </div>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Roll</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Student</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Marks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($students as $student)
                            <tr>
                                <td class="px-5 py-2.5 text-gray-500">{{ $student->currentEnrollment?->roll_no ?? '-' }}</td>
                                <td class="px-5 py-2.5">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $student->full_name() }}</span>
                                    @if ($existing->has($student->id))
                                        <a href="{{ route('exams.report-card', [$exam, $student]) }}" class="ms-2 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">report card</a>
                                    @endif
                                </td>
                                <td class="px-5 py-2.5">
                                    <input type="number" name="marks[{{ $student->id }}]" min="0" step="0.01"
                                           value="{{ old('marks.'.$student->id, $existing->get($student->id)?->marks_obtained) }}"
                                           placeholder="absent"
                                           class="w-28 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" />
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3"><x-empty-state title="No students in this class" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($students->isNotEmpty())
                    <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Save Marks</button>
                        <span class="text-xs text-gray-400 ms-2">Leave empty to store 0 (e.g. absent).</span>
                    </div>
                @endif
            </div>
        </form>
    @endif
</x-app-layout>
