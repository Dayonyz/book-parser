<?php

namespace App\Services\Parsers;

use App\Exceptions\InvalidEntryTransformerException;
use App\Services\Parsers\Contracts\JsonEntryTransformer;
use App\Services\Parsers\ValueObjects\TransformedField;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\ArrayShape;

class BookJsonEntryTransformer extends JsonEntryTransformer
{
    #[ArrayShape([
        'isbn' => "\Closure",
        'title' => "\Closure",
        'shortDescription' => "\Closure",
        'longDescription' => "\Closure",
        'authors' => "\Closure",
        'publishedDate' => "\Closure"
    ])]
    protected static function getTransformMaps(): array
    {
        return [
            'isbn' => function ($entry) {
                $data = ['isbn' => $entry['isbn'] ?? null];
                $data['isbn'] = $data['isbn'] ? trim(str_replace(['-', ' '], '', $data['isbn'])) : null;

                $validator = Validator::make(
                    $data,
                    ['isbn' => ['required', 'string', 'isbn']]
                );

                if ($validator->fails()) {
                    throw new InvalidEntryTransformerException(
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
                $data = ['title' => $entry['title'] ?? null];
                $data['title'] = $data['title'] ? trim($data['title']) : null;

                $validator = Validator::make(
                    $data,
                    ['title' => ['required', 'string']]
                );

                if ($validator->fails()) {
                    throw new InvalidEntryTransformerException(
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
                    throw new InvalidEntryTransformerException(
                        "Missing or invalid 'authors'",
                        $entry
                    );
                }

                $filteredAuthors =  array_filter(
                    array_map('trim', array_filter($entry['authors'], 'is_string')),
                    fn($v) => $v !== ''
                );

                $normalizeAuthors = function (array $rawAuthors):array {
                    $result = [];

                    $excluded = ['friends', 'editors'];

                    foreach ($rawAuthors as $authorEntry) {
                        $entry = preg_replace('/\b(with contributions by|with|and)\b/i', ',', $authorEntry);

                        $authors = array_filter(array_map('trim', explode(',', $entry)));

                        foreach ($authors as $author) {
                            $authorNormalized = strtolower(trim($author));

                            if ($authorNormalized === '' || in_array($authorNormalized, $excluded, true)) {
                                continue;
                            }

                            $result[] = $author;
                        }
                    }

                    return array_values(array_unique($result));
                };

                $filteredAuthors = $normalizeAuthors($filteredAuthors);

                if (empty($filteredAuthors)) {
                    throw new InvalidEntryTransformerException(
                        "Missing or invalid 'authors'",
                        $entry
                    );
                }

                return new TransformedField('authors', $filteredAuthors);
            },
            'publishedDate' => function($entry) {
                if (!isset($entry['publishedDate']) || !isset($entry['publishedDate']['$date'])) {
                    throw new InvalidEntryTransformerException(
                        "Missing 'publishedDate' field",
                        $entry
                    );
                }

                try {
                    $date = new DateTime($entry['publishedDate']['$date']);
                } catch (Exception) {
                    throw new InvalidEntryTransformerException(
                        "Invalid 'publishedDate' format",
                        $entry
                    );
                }

                return new TransformedField('published_at', $date);
            }
        ];
    }
}