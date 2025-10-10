<?php

namespace App\Services\Parsers;

use App\Services\Parsers\Contracts\IterableParser;
use App\Services\Parsers\Contracts\RemoteJsonIterableParser;

class BookRemoteJsonParser extends RemoteJsonIterableParser implements IterableParser
{
    private static ?BookRemoteJsonParser $instance = null;

    private function __construct()
    {
        $url = config('app.parser.book.remote_url');

        parent:: __construct(
            $url,
            new BookMappedEntryTransformer(),
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
}