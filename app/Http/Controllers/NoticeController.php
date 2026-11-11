<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoticeRequest;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $notices = Notice::query()
            ->when(! $user->isAdmin(), fn ($q) => $q->visibleTo($user))
            ->when($request->input('search'), function ($q, $term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('body', 'like', "%{$term}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('notices.index', compact('notices'));
    }

    public function create()
    {
        return view('notices.form', ['notice' => new Notice()]);
    }

    public function store(StoreNoticeRequest $request)
    {
        Notice::create($request->validated() + [
            'published_by' => $request->user()->id,
            'is_active'    => $request->boolean('is_active', true),
        ]);

        return redirect()->route('notices.index')->with('success', 'Notice published.');
    }

    public function show(Notice $notice)
    {
        $notice->load('publisher');

        return view('notices.show', compact('notice'));
    }

    public function edit(Notice $notice)
    {
        return view('notices.form', compact('notice'));
    }

    public function update(StoreNoticeRequest $request, Notice $notice)
    {
        $notice->update($request->validated() + [
            'is_active' => $request->boolean('is_active', false),
        ]);

        return redirect()->route('notices.index')->with('success', 'Notice updated.');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();

        return redirect()->route('notices.index')->with('success', 'Notice deleted.');
    }
}
