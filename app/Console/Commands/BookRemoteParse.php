<?php

namespace App\Console\Commands;

use App\Services\Importers\BookImporter;
use App\Services\Parsers\BookJsonParser;
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
        $parser = BookJsonParser::makeInstance();

        $imported = 0;
        foreach ($parser->iterateEntries() as $entry) {
            try {
                BookImporter::import($entry);
                $imported++;
            } catch (\Throwable $e) {
                Log::error("Import failed: " . $e->getMessage());
            }
        }

        $this->info("Imported: $imported, Skipped: check logs");
    }
}
