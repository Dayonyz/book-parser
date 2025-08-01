<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('author_book')) {
            Schema::create('author_book', function (Blueprint $table) {
                $table->foreignId('author_id')->constrained()->cascadeOnDelete();
                $table->foreignId('book_id')->constrained()->cascadeOnDelete();
                $table->primary(['author_id', 'book_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('author_book');
    }
};
