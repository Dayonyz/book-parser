<?php

namespace App\Services\Parsers\DTOs;

class EntryTransformed
{
    public function __construct(
        public readonly bool         $success,
        public readonly ?array       $entry,
        public readonly string       $error = '',
        public readonly array|string $raw = '',
    ){
    }
}