<x-app-layout title="Timetable">
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
                    <option value="">All / common</option>
                    @foreach ($sections as $s)
                        <option value="{{ $s->id }}" @selected($section?->id === $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if (! $class)
        <x-empty-state title="No classes yet" description="Create classes to build a timetable." />
    @else
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
            {{-- Days table (Sun-Thu school days in Bangladesh + weekend Sat shown if entries exist) --}}
            <div class="lg:col-span-3 space-y-4">
                @foreach ([0, 1, 2, 3, 4] as $day)
                    @if ($entries->has($day) || $day <= 4)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="font-semibold text-gray-900 dark:text-gray-100">{{ \App\Models\TimetableEntry::DAYS[$day] }}</h2>
                            </div>
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($entries->get($day, collect()) as $entry)
                                    <div class="px-5 py-3 flex items-center justify-between text-sm">
                                        <div class="flex items-center gap-4">
                                            <span class="font-mono text-xs text-gray-500">{{ $entry->start_time->format('H:i') }} - {{ $entry->end_time->format('H:i') }}</span>
                                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $entry->subject?->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                            @if ($entry->room)<span>Room {{ $entry->room }}</span>@endif
                                            <span>{{ $entry->teacher?->full_name() }}</span>
                                            @role('admin')
                                                <form method="POST" action="{{ route('timetable.destroy', $entry) }}" onsubmit="return confirm('Remove this period?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:underline">Remove</button>
                                                </form>
                                            @endrole
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-5 py-3 text-sm text-gray-400">No periods scheduled.</div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Add entry form --}}
            @role('admin')
                <div class="lg:col-span-2">
                    <form method="POST" action="{{ route('timetable.store') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                        @csrf
                        <h2 class="font-semibold text-gray-900 dark:text-gray-100">Add Period</h2>
                        <input type="hidden" name="school_class_id" value="{{ $class->id }}">
                        <input type="hidden" name="section_id" value="{{ $section?->id }}">
                        <div>
                            <x-input-label for="tt_subject" value="Subject *" />
                            <select id="tt_subject" name="subject_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                                <option value="">Select</option>
                                @foreach ($class->subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="tt_teacher" value="Teacher" />
                            <select id="tt_teacher" name="teacher_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">Not assigned</option>
                                @foreach (\App\Models\Teacher::where('is_active', true)->orderBy('first_name')->get() as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->full_name() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-input-label for="tt_day" value="Day *" />
                                <select id="tt_day" name="day_of_week" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                                    @foreach (\App\Models\TimetableEntry::DAYS as $idx => $dayName)
                                        <option value="{{ $idx }}">{{ $dayName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="tt_room" value="Room" />
                                <x-text-input id="tt_room" name="room" type="text" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="tt_start" value="Start *" />
                                <input type="time" id="tt_start" name="start_time" value="09:00" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" required />
                            </div>
                            <div>
                                <x-input-label for="tt_end" value="End *" />
                                <input type="time" id="tt_end" name="end_time" value="10:00" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" required />
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                        <button class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Add Period</button>
                    </form>
                </div>
            @endrole
        </div>
    @endif
</x-app-layout>
