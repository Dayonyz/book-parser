<?php

namespace App\Services\Parsers\Contracts;

use App\Services\Parsers\Exceptions\InvalidEntryException;
use App\Services\Parsers\Contracts\IterableParser as ParserContract;
use App\Services\Parsers\DTOs\EntryTransformed;
use InvalidArgumentException;
use Exception;
use Generator;

abstract class RemoteJsonIterableParser implements ParserContract
{
    protected string $url;
    protected EntryTransformer $transformer;
    protected ?Downloader $downloader;

    public function __construct(
        string                $url,
        EntryTransformer      $transformer,
        ?Downloader $downloader = null
    ) {
        $this->setUrl($url);
        $this->transformer = $transformer;
        $this->downloader = $downloader;
    }

    protected function setUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Incorrect URL for parsing");
        }

        $this->url = $url;
    }

    /**
     * @return Generator<EntryTransformed>
     *@throws Exception
     */
    public function iterateEntries(): Generator
    {
        $this->downloader?->download();

        $handle = fopen($this->downloader ? $this->downloader->getFilePath() : $this->url, 'r');

        if (!$handle) {
            throw new Exception($this->downloader
                ? "Cannot open open downloaded file '{$this->downloader->getFilePath()}'"
                : "Cannot open remote resource '{$this->url}'"
            );
        }

        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);

                if ($line === '') continue;

                $entryDecoded = json_decode($line, true);

                if (!$entryDecoded) {
                    yield new EntryTransformed(
                        false,
                        null,
                        "Invalid JSON",
                        $line
                    );

                    continue;
                }

                try {
                    $transformedEntry = $this->transformer->transform($entryDecoded);
                    yield new EntryTransformed(
                        true,
                        $transformedEntry,
                    );
                } catch (InvalidEntryException $exception) {
                    yield new EntryTransformed(
                        false,
                        null,
                        $exception->getMessage(),
                        $exception->getInvalidData()
                    );
                }
            }
        } finally {
            fclose($handle);
        }
    }
}