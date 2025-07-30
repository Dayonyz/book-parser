<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookResource;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return AuthorResource::collection(
            Author::withCount('books')
                ->filter($request->only('q'))
                ->paginate(20)
        );
    }

    public function books(Request $request, Author $author): AnonymousResourceCollection
    {
        return BookResource::collection(
            $author->books()
                ->with('authors')
                ->filter($request->only('q'))
                ->paginate(20)
        );
    }
}
