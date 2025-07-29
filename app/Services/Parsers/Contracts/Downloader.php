<?php

namespace App\Services\Parsers\Contracts;

interface Downloader
{
    public function download(): void;

    public function getFilePath(): string;
}