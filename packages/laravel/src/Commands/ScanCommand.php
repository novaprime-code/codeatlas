<?php

declare(strict_types=1);

namespace CodeAtlas\Laravel\Commands;

use CodeAtlas\Scanner\Scanner;
use Illuminate\Console\Command;
use Throwable;

/**
 * php artisan codeatlas:scan
 *
 * Discovery-only dry run: shows what the scanner finds without parsing
 * or analyzing anything. Useful to validate scan paths and exclusions.
 */
final class ScanCommand extends Command
{
    protected $signature = 'codeatlas:scan';

    protected $description = 'Discover analyzable files without running any analyzers';

    public function handle(): int
    {
        $basePath = $this->laravel->basePath();

        try {
            $context = Scanner::default()->scan($basePath);
        } catch (Throwable $e) {
            $this->components->error("Scan failed: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->components->twoColumnDetail('Project', $context->name);
        $this->components->twoColumnDetail('Framework', $context->framework . ' ' . ($context->frameworkVersion ?? ''));
        $this->components->twoColumnDetail('Total files', (string) $context->fileCount());

        foreach ($context->fileCounts() as $type => $count) {
            $this->components->twoColumnDetail("  {$type}", (string) $count);
        }

        return self::SUCCESS;
    }
}
