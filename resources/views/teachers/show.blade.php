<x-app-layout :title="$teacher->full_name()">
    <x-slot name="actions">
        <a href="{{ route('teachers.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
        @can('update', $teacher)
            <a href="{{ route('teachers.edit', $teacher) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Edit</a>
        @endcan
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-full bg-violet-100 dark:bg-violet-900/50 text-violet-600 dark:text-violet-300 flex items-center justify-center text-2xl font-bold">
                    {{ strtoupper(substr($teacher->first_name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $teacher->full_name() }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $teacher->employee_id }} · {{ $teacher->qualification ?? 'No qualification listed' }}</p>
                </div>
                <div class="ms-auto">
                    @if ($teacher->is_active)
                        <x-badge type="green">Active</x-badge>
                    @else
                        <x-badge type="gray">Inactive</x-badge>
                    @endif
                </div>
            </div>
            <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 text-sm">
                <div><dt class="text-gray-500 dark:text-gray-400">Phone</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-200">{{ $teacher->phone ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Email</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-200">{{ $teacher->email ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Joined</dt><dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-200">{{ $teacher->join_date?->format('d M Y') ?? '-' }}</dd></div>
            </dl>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Subjects Taught</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($teacher->classSubjects as $cs)
                        <div class="px-5 py-3 flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $cs->subject?->name }}</span>
                            <x-badge type="sky">{{ $cs->schoolClass?->name }}</x-badge>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-gray-500">No subjects assigned yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Class Teacher Of</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($teacher->sections as $section)
                        <div class="px-5 py-3 flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $section->schoolClass?->name }} · {{ $section->name }}</span>
                            <x-badge type="violet">Capacity {{ $section->capacity }}</x-badge>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-gray-500">Not a class teacher.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
