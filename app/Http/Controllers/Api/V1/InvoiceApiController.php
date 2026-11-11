<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $query = Invoice::with(['student', 'schoolClass', 'feeType']);

        if ($user->isStudent() && $user->student) {
            $query->where('student_id', $user->student->id);
        } elseif ($user->isParent()) {
            $query->whereIn('student_id', $user->children()->pluck('id'));
        }

        $query->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('student_id'), function ($q, $studentId) use ($user) {
                if ($user->isStudent()) {
                    return; // already scoped
                }
                if ($user->isParent() && ! $user->children()->pluck('id')->contains($studentId)) {
                    abort(403, 'You may only view invoices of your own children.');
                }
                $q->where('student_id', $studentId);
            });

        $invoices = $query->latest()->paginate(min((int) $request->query('per_page', 20), 100));

        return InvoiceResource::collection($invoices);
    }

    public function show(Request $request, Invoice $invoice): InvoiceResource
    {
        $request->user()->can('view', $invoice) ?: abort(403);

        return new InvoiceResource($invoice->loadMissing(['student', 'schoolClass', 'feeType', 'payments']));
    }
}
