<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $books = Book::with('authors')
            ->filter($request->only(['q', 'author_id', 'author']))
            ->get();

        return response()->json(BookResource::collection($books));
    }
}
