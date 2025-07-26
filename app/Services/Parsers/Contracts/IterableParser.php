<?php

namespace App\Services\Parsers\Contracts;

use Generator;

interface IterableParser
{
    public function iterateEntries(): Generator;
}