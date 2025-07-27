<?php

namespace App\Services\Parsers;

use Exception;
use Illuminate\Support\Facades\Process;

class RemoteJsonDownloader
{
    public function __construct(
        private readonly string $url,
        private readonly string $path
    ){
    }

    /**
     * @throws Exception
     */
    public function download(): void
    {
        $response = Process::run(
            "curl {$this->url} | jq -c '.[]' > {$this->getFilePath()}"
        );

        if (!$response->successful() || !file_exists($this->getFilePath())) {
            throw new Exception("Cannot download remote resource '{$this->url}'");
        }
    }

    public function getFilePath(): string
    {
        return base_path($this->path);
    }
}