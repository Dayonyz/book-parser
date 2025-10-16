<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('books')) {
            Schema::create('books', function (Blueprint $table) {
                $table->id();
                $table->string('isbn')->unique();
                $table->string('title');
                $table->text('short_description')->nullable();
                $table->longText('description')->nullable();
                $table->dateTime('published_at')->nullable();
                $table->timestamps();
            });

            DB::statement('CREATE FULLTEXT INDEX ft_title_ngram ON books(title) WITH PARSER ngram');
            DB::statement('CREATE FULLTEXT INDEX ft_short_description_ngram ON books(short_description) WITH PARSER ngram');
            DB::statement('CREATE FULLTEXT INDEX ft_description_ngram ON books(description) WITH PARSER ngram');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX ft_title_ngram ON books');
        DB::statement('DROP INDEX ft_short_description_ngram ON books');
        DB::statement('DROP INDEX ft_description_ngram ON books');

        Schema::dropIfExists('books');
    }
};
