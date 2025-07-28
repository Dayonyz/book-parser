<?php

namespace App\Services\Parsers\Contracts;

use App\Exceptions\InvalidEntryTransformerException;

interface EntryTransformer
{
    /**
     * @throws InvalidEntryTransformerException
     */
    public function transform(array $entry): array;
}