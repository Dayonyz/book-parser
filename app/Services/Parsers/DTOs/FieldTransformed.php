<?php

namespace App\Services\Parsers\DTOs;

class FieldTransformed
{
    public function __construct(
        public readonly string $name,
        public readonly mixed $value
    ){
    }
}