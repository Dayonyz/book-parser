<?php

namespace App\Services\Parsers;

use App\Services\Parsers\Contracts\IterableParser;
use Exception;
use Generator;

class BookIterableParserProxy implements IterableParser
{
    private RemoteJsonIterableParser $parser;
    private static ?BookIterableParserProxy $instance = null;

    private function __construct()
    {
        $url = config('app.parser.book.remote_url');

        $this->parser = new RemoteJsonIterableParser(
            $url,
            new BookJsonEntryTransformer(),
            new RemoteJsonDownloader($url, config('app.parser.book.local_path'))
        );
    }

    private function __clone(): void
    {

    }

    public static function makeInstance(): static
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @throws Exception
     */
    public function iterateEntries(): Generator
    {
        foreach ($this->parser->iterateEntries() as $bookEntry) {
            yield $bookEntry;
        }
    }
}