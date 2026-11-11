<x-app-layout title="New Invoice">
    <x-slot name="actions">
        <a href="{{ route('invoices.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Back</a>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Single invoice -->
        <form method="POST" action="{{ route('invoices.store') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            @csrf
            <h2 class="font-semibold text-gray-900 dark:text-gray-100">Single Invoice</h2>
            <div>
                <x-input-label for="student_id" value="Student *" />
                <select id="student_id" name="student_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                    <option value="">Select student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">{{ $student->full_name() }} ({{ $student->admission_no }})</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-input-label for="fee_type_id" value="Fee Type *" />
                    <select id="fee_type_id" name="fee_type_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                        <option value="">Select</option>
                        @foreach ($feeTypes as $feeType)
                            <option value="{{ $feeType->id }}" data-amount="{{ $feeType->amount }}">{{ $feeType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="month" value="Month (for monthly fees)" />
                    <input type="month" id="month" name="month" value="{{ old('month', now()->format('Y-m')) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" />
                </div>
                <div>
                    <x-input-label for="amount" value="Amount (BDT) *" />
                    <x-text-input id="amount" name="amount" type="number" min="0" step="0.01" :value="old('amount')" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="discount" value="Discount" />
                    <x-text-input id="discount" name="discount" type="number" min="0" step="0.01" :value="old('discount', 0)" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="due_date" value="Due Date" />
                    <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" />
                </div>
            </div>
            <input type="hidden" name="school_class_id" value="{{ $students->first()?->currentEnrollment?->school_class_id ?? \App\Models\SchoolClass::first()?->id }}">
            <input type="hidden" name="academic_year_id" value="{{ \App\Models\AcademicYear::current()?->id ?? $years->first()?->id }}">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Create Invoice</button>
            <p class="text-xs text-gray-400">The class and academic year default to the student's current enrollment.</p>
        </form>

        <!-- Bulk generate -->
        <form method="POST" action="{{ route('invoices.generate') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            @csrf
            <h2 class="font-semibold text-gray-900 dark:text-gray-100">Generate Monthly Invoices for a Class</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400">Creates one invoice per active student. Students who already have an invoice for the fee type and month are skipped.</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <x-input-label for="g_class" value="Class *" />
                    <select id="g_class" name="school_class_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                        <option value="">Select</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="g_year" value="Academic Year *" />
                    <select id="g_year" name="academic_year_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}" @selected($year->is_current)>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="g_fee" value="Fee Type *" />
                    <select id="g_fee" name="fee_type_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                        <option value="">Select</option>
                        @foreach ($feeTypes as $feeType)
                            <option value="{{ $feeType->id }}">{{ $feeType->name }} (BDT {{ number_format($feeType->amount) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="g_month" value="Month" />
                    <input type="month" id="g_month" name="month" value="{{ now()->format('Y-m') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" />
                </div>
                <div class="col-span-2">
                    <x-input-label for="g_due" value="Due Date" />
                    <x-text-input id="g_due" name="due_date" type="date" class="mt-1 block w-full" />
                </div>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 text-white text-sm font-medium rounded-lg">Generate Invoices</button>
        </form>
    </div>

    <script>
        document.getElementById('fee_type_id')?.addEventListener('change', function () {
            const amount = this.selectedOptions[0]?.dataset.amount;
            if (amount) document.getElementById('amount').value = amount;
        });
    </script>
</x-app-layout>
