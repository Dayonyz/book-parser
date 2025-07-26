<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request)
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

