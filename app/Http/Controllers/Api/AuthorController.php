<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookResource;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $authors = Author::withCount('books')
            ->filter($request->only('q'))
            ->paginate(20);

        return response()->json(AuthorResource::collection($authors));
    }

    public function books(Request $request, Author $author): JsonResponse
    {
        $books = $author->books()
            ->with('authors')
            ->filter($request->only('q'))
            ->paginate(20);

        return response()->json(BookResource::collection($books));
    }
}
