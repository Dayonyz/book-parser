<?php

namespace App\Services\Importers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class BookImporter
{
    public static function import(array $parsedBook): void
    {
        DB::transaction(function () use ($parsedBook) {
            $book = Book::updateOrCreate(
                ['isbn' => $parsedBook['isbn']],
                array_diff_key($parsedBook, array_flip(['authors']))
            );

            $authorIds = collect($parsedBook['authors'])->map(function (string $authorName) {
                return Author::firstOrCreate(['name' => $authorName])->id;
            });

            $book->authors()->sync($authorIds);
        });
    }
}