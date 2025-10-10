<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

class BookResource extends JsonResource
{
    #[ArrayShape(['title' => "mixed", 'short_description' => "mixed", 'description' => "mixed", 'authors' => "mixed", 'published_at' => "mixed"])] public function toArray($request)
    {
        return [
            'title' => $this->title,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'authors' => $this->authors->pluck('name'),
            'published_at' => optional($this->published_at)->toDateString(),
        ];
    }
}

