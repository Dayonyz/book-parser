<?php

namespace App\Services\Parsers\Contracts;

use App\Services\Parsers\Exceptions\InvalidEntryException;
use App\Services\Parsers\Dto\TransformedField;
use InvalidArgumentException;

abstract class MappedEntryTransformer implements EntryTransformer
{
    private array $transformMaps;

    public function __construct(array $transformMaps = [])
    {
        $this->setTransformMaps(!empty($transformMaps) ? $transformMaps : $this->getTransformMaps());
    }

    abstract protected function getTransformMaps(): array;

    protected function setTransformMaps(array $transformMaps)
    {
        $this->validateTransformMaps($transformMaps);

        $this->transformMaps = $transformMaps;
    }

    /**
     * @param array $maps
     * @return void
     */
    protected function validateTransformMaps(array $maps): void
    {
        foreach ($maps as $key => $transformer) {
            if (!is_string($key)) {
                throw new InvalidArgumentException(
                    "Transformer key '{$key}' must be a string, " . gettype($key) . " given"
                );
            }

            if (!is_callable($transformer)) {
                throw new InvalidArgumentException("Transformer for '$key' must be a Closure or callable");
            }
        }
    }

    /**
     * @throws InvalidEntryException
     */
    public function transform(array $entry): array
    {
        //transform maps array is empty - returns as it passed
        if (empty($this->transformMaps)) {

            return $entry;
        }

        $resultEntry = [];

        foreach ($this->transformMaps as $fieldKey => $transformCallback) {
            $result = $transformCallback($entry);

            if ($result instanceof TransformedField) {
                $resultEntry[$result->name] = $result->value;
            } else {
                $resultEntry[$fieldKey] = $result;
            }
        }

        return $resultEntry;
    }
}