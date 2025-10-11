<?php

namespace App\Services\Parsers\Contracts;

use App\Services\Parsers\Exceptions\InvalidEntryException;
use App\Services\Parsers\Dto\TransformedField;
use InvalidArgumentException;
use ReflectionException;
use ReflectionFunction;
use ReflectionNamedType;

abstract class MappedEntryTransformer implements EntryTransformer
{
    private array $transformMaps;

    /**
     * @throws ReflectionException
     */
    public function __construct(array $transformMaps = [])
    {
        $this->setTransformMaps(!empty($transformMaps) ? $transformMaps : $this->getTransformMaps());
    }

    abstract protected function getTransformMaps(): array;

    /**
     * @throws ReflectionException
     */
    protected function setTransformMaps(array $transformMaps)
    {
        $this->validateTransformMaps($transformMaps);

        $this->transformMaps = $transformMaps;
    }

    /**
     * @param array $maps
     * @return void
     * @throws ReflectionException
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

            $reflection = new ReflectionFunction($transformer);
            $returnType = $reflection->getReturnType();

            if ($returnType === null) {
                throw new InvalidArgumentException("Transformer for '$key' must declare a return type");
            }

            if ($returnType instanceof ReflectionNamedType) {
                $typeName = $returnType->getName();

                if ($typeName !== TransformedField::class) {
                    throw new InvalidArgumentException(
                        "Transformer for '$key' must return instance of " .
                        TransformedField::class .
                        ", got '$typeName'"
                    );
                }
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
            $transformed = $transformCallback($entry);

            $resultEntry[$transformed->name] = $transformed->value;
        }

        return $resultEntry;
    }
}