<?php

namespace App\Exceptions;

use Exception;

class InvalidEntryTransformerException extends Exception
{
    protected array $invalidData;

    public function __construct(string $message, array $invalidData = [])
    {
        parent::__construct($message);

        $this->invalidData = $invalidData;
    }

    public function getInvalidData(): array
    {
        return $this->invalidData;
    }
}
