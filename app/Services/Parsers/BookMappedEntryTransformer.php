<?php

namespace App\Services\Parsers;

use App\Services\Parsers\Exceptions\InvalidEntryException;
use App\Services\Parsers\Contracts\MappedEntryTransformer;
use App\Services\Parsers\Dto\TransformedField;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\ArrayShape;
use DateTime;
use Exception;

class BookMappedEntryTransformer extends MappedEntryTransformer
{
    #[ArrayShape([
        'isbn' => "\Closure",
        'title' => "\Closure",
        'shortDescription' => "\Closure",
        'longDescription' => "\Closure",
        'authors' => "\Closure",
        'publishedDate' => "\Closure"
    ])]
    protected function getTransformMaps(): array
    {
        return [
            'isbn' => function ($entry) {
                $data = [];
                $data['isbn'] = isset($entry['isbn']) && $entry['isbn'] ?
                    preg_replace('/[\s\-]+/u', '', $entry['isbn']) :
                    null;

                $validator = Validator::make(
                    $data,
                    ['isbn' => ['required', 'string', 'isbn']]
                );

                if ($validator->fails()) {
                    throw new InvalidEntryException(
                        "Validation failed for 'isbn' field while parsing",
                        [
                            'entry' => $entry,
                            'errors' => $validator->errors()->all(),
                        ]
                    );
                }

                return new TransformedField('isbn', $data['isbn']);
            },
            'title' => function ($entry) {
                $data = ['title' => trim($entry['title'] ?? '') ?: null];

                $validator = Validator::make(
                    $data,
                    ['title' => ['required', 'string']]
                );

                if ($validator->fails()) {
                    throw new InvalidEntryException(
                        "Validation failed for 'title' field while parsing",
                        [
                            'entry' => $entry,
                            'errors' => $validator->errors()->all(),
                        ]
                    );
                }

                return new TransformedField('title', $data['title']);
            },
            'shortDescription' => function ($entry) {
                $shortDescription =  $entry['shortDescription'] ?? null;

                if ($shortDescription) {
                    $shortDescription = trim($shortDescription);
                }

                return new TransformedField('short_description', $shortDescription);
            },
            'longDescription' => function ($entry) {
                $longDescription =  $entry['longDescription'] ?? null;

                if ($longDescription) {
                    $longDescription = trim($longDescription);
                }

                return new TransformedField('description', $longDescription);
            },
            'authors' => function($entry) {
                if (!isset($entry['authors']) || !is_array($entry['authors'])) {
                    throw new InvalidEntryException(
                        "Missing or invalid 'authors'",
                        $entry
                    );
                }

                $filteredAuthors =  array_filter(
                    array_map('trim', array_filter($entry['authors'], 'is_string')),
                    fn($v) => $v !== ''
                );

                $normalizeAuthors = function (array $authors): array {
                    $result = [];

                    foreach ($authors as $author) {
                        $cleaned = preg_replace('/\b(friends|editors|foreword by)\b/i', '', $author);

                        $parts = preg_split(
                            '/\b(?:and|with|edited by|writing as|contributions by|;)\b/i',
                            $cleaned,
                            flags: PREG_SPLIT_NO_EMPTY
                        );

                        foreach ($parts as $part) {
                            $part = trim($part, " \t\n\r\0\x0B;");

                            if ($part === '' || is_numeric($part)) {
                                continue;
                            }

                            $result[] = $part;
                        }
                    }

                    return array_values(array_unique($result));
                };

                $filteredAuthors = $normalizeAuthors($filteredAuthors);

                if (empty($filteredAuthors)) {
                    throw new InvalidEntryException(
                        "Missing or invalid 'authors'",
                        $entry
                    );
                }

                return new TransformedField('authors', $filteredAuthors);
            },
            'publishedDate' => function($entry) {
                if (isset($entry['publishedDate']['$date'])) {
                    try {
                        $date = new DateTime($entry['publishedDate']['$date']);
                    } catch (Exception) {
                        throw new InvalidEntryException(
                            "Invalid 'publishedDate' format",
                            $entry
                        );
                    }
                } else {
                    $date = null;
                }

                return new TransformedField('published_at', $date);
            }
        ];
    }
}