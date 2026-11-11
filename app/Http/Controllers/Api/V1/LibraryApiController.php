<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LibraryApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $books = Book::search($request->query('search'))
            ->orderBy('title')
            ->paginate(min((int) $request->query('per_page', 20), 100));

        return BookResource::collection($books);
    }
}
