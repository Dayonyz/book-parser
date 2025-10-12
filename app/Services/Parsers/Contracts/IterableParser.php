<?php

namespace App\Services\Parsers\Contracts;

use App\Services\Parsers\DTOs\EntryTransformed;
use Generator;

interface IterableParser
{
    /**
     * @return Generator<EntryTransformed>
     */
    public function iterateEntries(): Generator;
}