<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with('authors')
            ->filter($request->only(['q', 'author_id', 'author']))
            ->get();

        return response()->json($books);
    }
}
