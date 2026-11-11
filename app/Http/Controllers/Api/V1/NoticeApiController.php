<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoticeResource;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NoticeApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $notices = Notice::visibleTo($request->user())
            ->when($request->query('search'), function ($q, $term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('body', 'like', "%{$term}%");
            })
            ->latest()
            ->paginate(min((int) $request->query('per_page', 20), 100));

        return NoticeResource::collection($notices);
    }
}
