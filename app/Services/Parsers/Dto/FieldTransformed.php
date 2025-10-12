<?php

namespace App\Services\Parsers\Dto;

class FieldTransformed
{
    public function __construct(
        public readonly string $name,
        public readonly mixed $value
    ){
    }
}