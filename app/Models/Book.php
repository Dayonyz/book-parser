<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    protected $fillable = [
        'isbn',
        'title',
        'short_description',
        'description',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when($filters['q'] ?? null, function (Builder $q, $search) {
            $q->where(function ($q2) use ($search) {
                $q2->where('title', 'like', "%$search%")
                    ->orWhere('short_description', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhereHas('authors', function ($a) use ($search) {
                        $a->where('authors.name', 'like', "%$search%");
                    });
            });
        });
    }

}
