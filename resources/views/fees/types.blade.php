<x-app-layout title="Fee Types">
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Add Fee Type</h2>
        <form method="POST" action="{{ route('fee-types.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            @csrf
            <div>
                <x-input-label for="ft_name" value="Name *" />
                <x-text-input id="ft_name" name="name" placeholder="e.g. Tuition Fee" class="mt-1 block w-full" required />
            </div>
            <div>
                <x-input-label for="ft_amount" value="Amount (BDT) *" />
                <x-text-input id="ft_amount" name="amount" type="number" min="0" step="0.01" class="mt-1 block w-full" required />
            </div>
            <div>
                <x-input-label for="ft_frequency" value="Frequency" />
                <select id="ft_frequency" name="frequency" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option value="monthly">Monthly</option>
                    <option value="one_time">One time</option>
                </select>
            </div>
            <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">Add</button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Name</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Amount</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Frequency</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Invoices</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($feeTypes as $feeType)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $feeType->name }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">BDT {{ number_format($feeType->amount, 2) }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ str_replace('_', ' ', ucfirst($feeType->frequency)) }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $feeType->invoices_count }}</td>
                        <td class="px-5 py-3">
                            @if ($feeType->is_active)
                                <x-badge type="green">Active</x-badge>
                            @else
                                <x-badge type="gray">Disabled</x-badge>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right space-x-2">
                            <form method="POST" action="{{ route('fee-types.destroy', $feeType) }}" class="inline" onsubmit="return confirm('Delete this fee type?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state title="No fee types" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700">{{ $feeTypes->links() }}</div>
    </div>
</x-app-layout>
