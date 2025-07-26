<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Book::with('authors');

        $query->when($request->input('q'), function ($query) use ($request) {
            $search = $request->input('q');
            $query->where('title', 'like', "%$search%")
                ->orWhere('short_description', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
        });

        $query->when($request->input('author_id'), function ($query) use ($request){
            $query->whereHas('authors', fn($q) => $q->where('authors.id', $request->input('author_id')));
        });

        $query->when($request->input('author'), function ($query) use ($request){
            $query->whereHas(
                'authors',
                fn($q) => $q->where('authors.name', 'like', "%{$request->input('author')}%")
            );
        });

        return BookResource::collection($query->paginate(20));
    }
}
