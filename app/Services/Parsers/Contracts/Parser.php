<?php

namespace App\Services\Parsers\Contracts;

use Generator;

interface Parser
{
    public function iterateEntries(): Generator;
}