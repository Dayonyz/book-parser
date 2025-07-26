<?php

namespace App\Services\Parsers\Contracts;

use App\Services\Parsers\ValueObjects\TransformedField;
use Exception;
use InvalidArgumentException;

abstract class JsonEntryTransformer implements ArrayTransformer
{
    private array $transformMaps;

    public function __construct(array $transformMaps = [])
    {
        $this->setTransformMaps(!empty($transformMaps) ? $transformMaps : static::getTransformMaps());
    }

    abstract protected static function getTransformMaps(): array;

    protected function setTransformMaps(array $transformMaps)
    {
        $this->validateTransformMaps($transformMaps);

        $this->transformMaps = $transformMaps;
    }

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
     * @throws Exception
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