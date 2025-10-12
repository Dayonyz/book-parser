<?php

namespace App\Services\Parsers\DTOs;

class EntryTransformed
{
    public function __construct(
        public readonly bool $success,
        public readonly ?array $data,
        public readonly string $error = '',
        public readonly array|string $raw = '',
    ){
    }
}