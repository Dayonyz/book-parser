<?php

namespace App\Services\Parsers\Contracts;

use App\Services\Parsers\Dto\EntryTransformed;
use Generator;

interface IterableParser
{
    /**
     * @return Generator<EntryTransformed>
     */
    public function iterateEntries(): Generator;
}