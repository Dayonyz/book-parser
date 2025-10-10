<?php

namespace App\Services\Parsers\Contracts;

use App\Services\Parsers\Exceptions\InvalidEntryException;

interface EntryTransformer
{
    /**
     * @throws InvalidEntryException
     */
    public function transform(array $entry): array;
}