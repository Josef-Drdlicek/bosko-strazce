<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RunCollector extends Command
{
    protected $signature = 'bosko:collect
        {command : Python CLI command (collect-all, collect-contracts, collect-subsidies, extract-text, extract-entities, enrich-entities, deduplicate, download-files)}
        {--timeout=600 : Timeout in seconds}';

    protected $description = 'Run a Python data collector command';

    private const ALLOWED_COMMANDS = [
        'collect-all',
        'collect-uredni-deska',
        'collect-zapisy',
        'collect-documents',
        'collect-archive',
        'collect-contracts',
        'collect-subsidies',
        'download-files',
        'extract-text',
        'extract-entities',
        'enrich-entities',
        'deduplicate',
    ];

    public function handle(): int
    {
        $command = $this->argument('command');

        if (! in_array($command, self::ALLOWED_COMMANDS)) {
            $this->error("Unknown command: {$command}");
            $this->info("Available: " . implode(', ', self::ALLOWED_COMMANDS));
            return self::FAILURE;
        }

        $pythonScript = base_path('../main.py');

        if (! file_exists($pythonScript)) {
            $this->error("Python script not found: {$pythonScript}");
            return self::FAILURE;
        }

        $this->info("Running: python main.py {$command}");

        $process = new Process(
            ['python', $pythonScript, $command],
            base_path('..'),
            timeout: (int) $this->option('timeout')
        );

        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if ($process->isSuccessful()) {
            $this->info("Command '{$command}' completed successfully.");
            return self::SUCCESS;
        }

        $this->error("Command '{$command}' failed with exit code {$process->getExitCode()}.");
        return self::FAILURE;
    }
}
