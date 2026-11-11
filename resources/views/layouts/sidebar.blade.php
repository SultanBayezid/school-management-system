@php $user = auth()->user(); @endphp
<!-- Mobile overlay -->
<div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden" x-transition.opacity @click="sidebarOpen = false" x-cloak></div>

<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-100 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out flex flex-col"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
    <div class="flex items-center gap-3 h-16 px-5 border-b border-slate-800">
        <div class="h-9 w-9 rounded-lg bg-indigo-600 flex items-center justify-center font-bold text-white">S</div>
        <div class="leading-tight">
            <div class="font-semibold text-sm">School Management</div>
            <div class="text-[11px] text-slate-400">System</div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 text-sm">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
            Dashboard
        </a>

        @if ($user && in_array($user->role, ['admin', 'teacher']))
            <div class="pt-4 pb-1 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">People</div>
            <a href="{{ route('students.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('students.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                Students
            </a>
            <a href="{{ route('teachers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('teachers.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                Teachers
            </a>
        @endif

        @if ($user && $user->role === 'admin')
            <div class="pt-4 pb-1 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Academics</div>
            <a href="{{ route('classes.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('classes.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
                Classes &amp; Sections
            </a>
            <a href="{{ route('subjects.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('subjects.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                Subjects
            </a>
            <a href="{{ route('academic-years.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('academic-years.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                Academic Years
            </a>
            <a href="{{ route('enrollments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('enrollments.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.65.536m9.337-9.337a3 3 0 11-4.243 4.243 3 3 0 014.243-4.243z"/></svg>
                Enrollments
            </a>
        @endif

        @if ($user && in_array($user->role, ['admin', 'teacher']))
            <div class="pt-4 pb-1 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Daily Operations</div>
            <a href="{{ route('attendance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('attendance.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Attendance
            </a>
            <a href="{{ route('exams.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('exams.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/></svg>
                Exams &amp; Results
            </a>
        @endif

        <a href="{{ route('timetable.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('timetable.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Timetable
        </a>

        <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('invoices.*', 'payments.*', 'fee-types.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
            Fees &amp; Payments
        </a>

        @if ($user && $user->role === 'admin')
            <a href="{{ route('fee-types.index') }}" class="flex items-center gap-3 px-3 py-2 pl-11 rounded-lg hover:bg-slate-800 {{ request()->routeIs('fee-types.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-400' }}">
                Fee Types
            </a>
        @endif

        <a href="{{ route('notices.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('notices.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 002.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125c-.621 0-1.125.504-1.125 1.125V18a2.25 2.25 0 002.25 2.25h13.5"/></svg>
            Notices
        </a>

        @if ($user && in_array($user->role, ['admin', 'teacher']))
            <div class="pt-4 pb-1 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Library</div>
            <a href="{{ route('books.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('books.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                Books
            </a>
            <a href="{{ route('book-issues.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('book-issues.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                Issued Books
            </a>
        @endif

        @if ($user && $user->role === 'admin')
            <div class="pt-4 pb-1 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Administration</div>
            <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('users.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                Users
            </a>
            <a href="{{ route('settings.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('settings.*') ? 'bg-slate-800 text-white font-medium' : 'text-slate-300' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>
        @endif
    </nav>

    <div class="border-t border-slate-800 px-5 py-3">
        <div class="text-xs text-slate-400">Signed in as</div>
        <div class="text-sm font-medium truncate">{{ $user?->name }}</div>
        <div class="text-[11px] text-slate-500">{{ $user?->roleLabel() }}</div>
    </div>
</aside>
