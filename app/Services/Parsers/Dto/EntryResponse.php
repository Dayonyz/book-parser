<?php

namespace App\Services\Parsers\Dto;

class EntryResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?array $data,
        public readonly string $error = '',
        public readonly array|string $raw = '',
    ){
    }
}