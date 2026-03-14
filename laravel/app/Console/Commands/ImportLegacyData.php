<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

class ImportLegacyData extends Command
{
    protected $signature = 'bosko:import {path? : Path to legacy SQLite database} {--fresh : Truncate all tables before import}';
    protected $description = 'Import data from the legacy Python SQLite database';

    public function handle(): int
    {
        $path = $this->argument('path') ?? base_path('../data/db/boskovice.db');

        if (! file_exists($path)) {
            $this->error("Database not found: {$path}");
            return self::FAILURE;
        }

        $legacy = new PDO("sqlite:{$path}");
        $legacy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($this->option('fresh')) {
            $this->truncateAll();
        }

        $this->importDocuments($legacy);
        $this->importAttachments($legacy);
        $this->importEntities($legacy);
        $this->importContracts($legacy);
        $this->importSubsidies($legacy);
        $this->importEntityLinks($legacy);

        $this->info('Import complete.');
        return self::SUCCESS;
    }

    private function importDocuments(PDO $legacy): void
    {
        $rows = $legacy->query('SELECT * FROM documents')->fetchAll(PDO::FETCH_ASSOC);
        $this->info("Importing " . count($rows) . " documents...");

        $bar = $this->output->createProgressBar(count($rows));

        foreach (array_chunk($rows, 100) as $chunk) {
            $inserts = [];
            foreach ($chunk as $row) {
                $inserts[] = [
                    'id' => $row['id'],
                    'source_url' => $row['source_url'],
                    'title' => $row['title'],
                    'section' => $row['section'],
                    'published_date' => $row['published_date'],
                    'valid_until' => $row['valid_until'] ?? null,
                    'department' => $row['department'] ?? null,
                    'fulltext' => $row['fulltext'] ?? null,
                    'duplicate_of' => $row['duplicate_of'] ?? null,
                    'collected_at' => $row['collected_at'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $bar->advance();
            }
            DB::table('documents')->insert($inserts);
        }

        $bar->finish();
        $this->newLine();
    }

    private function importAttachments(PDO $legacy): void
    {
        $rows = $legacy->query('SELECT * FROM attachments')->fetchAll(PDO::FETCH_ASSOC);
        $this->info("Importing " . count($rows) . " attachments...");

        $bar = $this->output->createProgressBar(count($rows));

        foreach (array_chunk($rows, 100) as $chunk) {
            $inserts = [];
            foreach ($chunk as $row) {
                $inserts[] = [
                    'id' => $row['id'],
                    'document_id' => $row['document_id'],
                    'url' => $row['url'],
                    'filename' => $row['filename'] ?? null,
                    'local_path' => $row['local_path'] ?? null,
                    'size_bytes' => $row['size_bytes'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $bar->advance();
            }
            DB::table('attachments')->insert($inserts);
        }

        $bar->finish();
        $this->newLine();
    }

    private function importEntities(PDO $legacy): void
    {
        $rows = $legacy->query('SELECT * FROM entities')->fetchAll(PDO::FETCH_ASSOC);
        $this->info("Importing " . count($rows) . " entities...");

        $bar = $this->output->createProgressBar(count($rows));

        foreach (array_chunk($rows, 100) as $chunk) {
            $inserts = [];
            foreach ($chunk as $row) {
                $inserts[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'entity_type' => $row['entity_type'],
                    'ico' => $row['ico'] ?? null,
                    'source' => $row['source'] ?? null,
                    'metadata_json' => $row['metadata_json'] ?? null,
                    'created_at' => $row['created_at'] ?? now(),
                    'updated_at' => now(),
                ];
                $bar->advance();
            }
            DB::table('entities')->insert($inserts);
        }

        $bar->finish();
        $this->newLine();
    }

    private function importContracts(PDO $legacy): void
    {
        $rows = $legacy->query('SELECT * FROM contracts')->fetchAll(PDO::FETCH_ASSOC);
        $this->info("Importing " . count($rows) . " contracts...");

        $bar = $this->output->createProgressBar(count($rows));

        foreach (array_chunk($rows, 100) as $chunk) {
            $inserts = [];
            foreach ($chunk as $row) {
                $inserts[] = [
                    'id' => $row['id'],
                    'external_id' => $row['external_id'],
                    'subject' => $row['subject'] ?? null,
                    'amount' => $row['amount'] ?? null,
                    'currency' => $row['currency'] ?? 'CZK',
                    'date_signed' => $row['date_signed'] ?? null,
                    'publisher_ico' => $row['publisher_ico'] ?? null,
                    'publisher_name' => $row['publisher_name'] ?? null,
                    'counterparty_ico' => $row['counterparty_ico'] ?? null,
                    'counterparty_name' => $row['counterparty_name'] ?? null,
                    'source_url' => $row['source_url'] ?? null,
                    'fulltext' => $row['fulltext'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $bar->advance();
            }
            DB::table('contracts')->insert($inserts);
        }

        $bar->finish();
        $this->newLine();
    }

    private function importSubsidies(PDO $legacy): void
    {
        $rows = $legacy->query('SELECT * FROM subsidies')->fetchAll(PDO::FETCH_ASSOC);
        $this->info("Importing " . count($rows) . " subsidies...");

        $bar = $this->output->createProgressBar(count($rows));

        foreach (array_chunk($rows, 100) as $chunk) {
            $inserts = [];
            foreach ($chunk as $row) {
                $inserts[] = [
                    'id' => $row['id'],
                    'external_id' => $row['external_id'],
                    'title' => $row['title'] ?? null,
                    'provider' => $row['provider'] ?? null,
                    'recipient_ico' => $row['recipient_ico'] ?? null,
                    'recipient_name' => $row['recipient_name'] ?? null,
                    'program' => $row['program'] ?? null,
                    'amount' => $row['amount'] ?? null,
                    'year' => $row['year'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $bar->advance();
            }
            DB::table('subsidies')->insert($inserts);
        }

        $bar->finish();
        $this->newLine();
    }

    private function truncateAll(): void
    {
        $this->info('Truncating all tables...');
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('entity_links')->truncate();
        DB::table('subsidies')->truncate();
        DB::table('contracts')->truncate();
        DB::table('entities')->truncate();
        DB::table('attachments')->truncate();
        DB::table('documents')->truncate();
        DB::statement('PRAGMA foreign_keys = ON');
    }

    private function importEntityLinks(PDO $legacy): void
    {
        $rows = $legacy->query('SELECT * FROM entity_links')->fetchAll(PDO::FETCH_ASSOC);
        $this->info("Importing " . count($rows) . " entity links...");

        $bar = $this->output->createProgressBar(count($rows));

        foreach (array_chunk($rows, 100) as $chunk) {
            $inserts = [];
            foreach ($chunk as $row) {
                $inserts[] = [
                    'id' => $row['id'],
                    'entity_id' => $row['entity_id'],
                    'linked_type' => $row['linked_type'],
                    'linked_id' => $row['linked_id'],
                    'role' => $row['role'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $bar->advance();
            }
            DB::table('entity_links')->insert($inserts);
        }

        $bar->finish();
        $this->newLine();
    }
}
