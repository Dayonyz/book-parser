<?php

namespace App\Console\Commands;

use App\Services\Importers\BookImporter;
use App\Services\Parsers\BookRemoteJsonParser;
use App\Services\Parsers\Dto\EntryResponse;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BookRemoteParse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:book-remote-parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse books to DB from remote resource';

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle()
    {
        $parser = BookRemoteJsonParser::makeInstance();

        $imported = 0;
        $skipped = 0;

        /**
         * @var EntryResponse $result
         */
        foreach ($parser->iterateEntries() as $result) {
            if ($result->success) {
                BookImporter::import($result->data);
                $imported++;
            } else {
                $skipped++;
                Log::warning("Entry skipped: {$result->error}", $result->raw ?? []);
            }
        }

        $this->info("Imported: $imported, Skipped: $skipped, check logs");
    }
}
