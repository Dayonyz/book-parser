<?php

namespace App\Services\Parsers\Contracts;

interface Transformer
{
    public function transform(array $entry): array;
}