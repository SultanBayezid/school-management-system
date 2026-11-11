<?php

namespace App\Http\Controllers;

use App\Models\FeeType;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    public function index()
    {
        $feeTypes = FeeType::withCount('invoices')->orderBy('name')->paginate(20);

        return view('fees.types', compact('feeTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'amount'    => ['required', 'numeric', 'min:0'],
            'frequency' => ['required', 'in:monthly,one_time'],
            'is_active' => ['boolean'],
        ]);

        FeeType::create($data + ['is_active' => (bool) ($data['is_active'] ?? true)]);

        return back()->with('success', 'Fee type created.');
    }

    public function update(Request $request, FeeType $feeType)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'amount'    => ['required', 'numeric', 'min:0'],
            'frequency' => ['required', 'in:monthly,one_time'],
            'is_active' => ['boolean'],
        ]);

        $feeType->update($data + ['is_active' => (bool) ($data['is_active'] ?? false)]);

        return back()->with('success', 'Fee type updated.');
    }

    public function destroy(FeeType $feeType)
    {
        if ($feeType->invoices()->exists()) {
            return back()->with('error', 'Cannot delete: invoices exist for this fee type.');
        }

        $feeType->delete();

        return back()->with('success', 'Fee type deleted.');
    }
}
