<x-app-layout title="Attendance">
    <x-slot name="actions">
        <a href="{{ route('attendance.report') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Monthly Report</a>
    </x-slot>

    <div class="mb-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
            <div>
                <x-input-label for="class_id" value="Class" />
                <select id="class_id" name="class_id" class="mt-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" onchange="this.form.submit()">
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" @selected($class?->id === $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="section_id" value="Section" />
                <select id="section_id" name="section_id" class="mt-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" onchange="this.form.submit()">
                    <option value="">All sections</option>
                    @foreach ($sections as $s)
                        <option value="{{ $s->id }}" @selected($section?->id === $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="date" value="Date" />
                <input type="date" id="date" name="date" value="{{ $date }}" max="{{ today()->toDateString() }}"
                       class="mt-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" onchange="this.form.submit()" />
            </div>
        </form>
    </div>

    @if (! $class)
        <x-empty-state title="No classes yet" description="Create classes first to record attendance." />
    @else
        <form method="POST" action="{{ route('attendance.store') }}">
            @csrf
            <input type="hidden" name="school_class_id" value="{{ $class->id }}">
            <input type="hidden" name="section_id" value="{{ $section?->id }}">
            <input type="hidden" name="attendance_date" value="{{ $date }}">

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-gray-100">{{ $class->name }} {{ $section ? '· '.$section->name : '' }}</h2>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }} · {{ $students->count() }} students</p>
                    </div>
                    <div class="flex gap-2 text-xs">
                        <button type="button" onclick="markAll('present')" class="px-3 py-1.5 rounded-md bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 font-medium">All Present</button>
                        <button type="button" onclick="markAll('absent')" class="px-3 py-1.5 rounded-md bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 font-medium">All Absent</button>
                    </div>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Roll</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Student</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($students as $student)
                            @php $current = $existing->get($student->id)?->status ?? 'present'; @endphp
                            <tr>
                                <td class="px-5 py-2.5 text-gray-500">{{ $student->currentEnrollment?->roll_no ?? '-' }}</td>
                                <td class="px-5 py-2.5 font-medium text-gray-900 dark:text-gray-100">{{ $student->full_name() }}</td>
                                <td class="px-5 py-2.5">
                                    <div class="flex gap-3 text-xs">
                                        @foreach (\App\Models\Attendance::STATUSES as $status)
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" name="statuses[{{ $student->id }}]" value="{{ $status }}"
                                                       @checked($current === $status)
                                                       class="att-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" />
                                                <span class="capitalize">{{ $status }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3"><x-empty-state title="No students" description="No active students in this class." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($students->isNotEmpty())
                    <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
                            {{ $existing->isEmpty() ? 'Save Attendance' : 'Update Attendance' }}
                        </button>
                        @if ($existing->isNotEmpty())
                            <span class="text-xs text-gray-400 ms-2">Attendance already recorded for this date.</span>
                        @endif
                    </div>
                @endif
            </div>
        </form>

        <script>
            function markAll(status) {
                document.querySelectorAll('.att-radio').forEach(function (radio) {
                    if (radio.value === status) radio.checked = true;
                });
            }
        </script>
    @endif
</x-app-layout>
