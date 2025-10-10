<?php

namespace App\Services\Parsers\Contracts;

use App\Services\Parsers\Dto\EntryResponse;
use Generator;

interface IterableParser
{
    /**
     * @return Generator<EntryResponse>
     */
    public function iterateEntries(): Generator;
}