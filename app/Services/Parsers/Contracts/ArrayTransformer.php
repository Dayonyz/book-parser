<?php

namespace App\Services\Parsers\Contracts;

interface ArrayTransformer
{
    public function transform(array $entry): array;
}