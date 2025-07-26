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
        $query = Author::withCount('books');

        if ($search = $request->input('q')) {
            $query->where('name', 'like', "%$search%");
        }

        return AuthorResource::collection($query->paginate(20));
    }

    public function books(Author $author): AnonymousResourceCollection
    {
        $books = $author->books()->with('authors')->paginate(20);

        return BookResource::collection($books);
    }

    public function show(Author $author): AuthorResource
    {
        return AuthorResource::make($author);
    }
}
