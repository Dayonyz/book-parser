<?php

namespace App\Services\Parsers;

use App\Exceptions\InvalidEntryTransformerException;
use App\Services\Parsers\Contracts\Transformer;
use App\Services\Parsers\Contracts\Parser as ParserContract;
use Exception;
use Generator;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class RemoteJsonParser implements ParserContract
{
    private string $url;
    private Transformer $transformer;
    private ?RemoteJsonDownloader $downloader;

    public function __construct(
        string $url,
        Transformer $transformer,
        ?RemoteJsonDownloader $downloader = null
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
     * @throws Exception
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

                if ($line === '') {
                    continue;
                }

                $entryDecoded = json_decode($line, true);

                if (!$entryDecoded) {
                    yield "Entry is skipped - Invalid JSON: $line" . PHP_EOL;
                    continue;
                }

                try {
                    $transformedEntry = $this->transformer->transform($entryDecoded);
                    yield $transformedEntry;

                } catch (InvalidEntryTransformerException $exception) {
                    Log::warning("Entry skipped: " . $exception->getMessage(), $exception->getInvalidData());
                    continue;
                }
            }
        } finally {
            fclose($handle);
        }
    }

}