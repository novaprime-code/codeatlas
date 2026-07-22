<?php

declare(strict_types=1);

namespace CodeAtlas\Laravel\Commands;

use CodeAtlas\Contracts\ValueObjects\ExportConfig;
use CodeAtlas\Contracts\ValueObjects\ScanConfig;
use CodeAtlas\Exporters\Json\JsonExporter;
use CodeAtlas\Laravel\AnalysisWriter;
use CodeAtlas\Laravel\CodeAtlasFactory;
use Illuminate\Console\Command;
use Throwable;

/**
 * php artisan codeatlas:analyze
 *
 * Runs the full pipeline (scan → analyze → export) against the current
 * Laravel application and writes the JSON document to the configured
 * output directory.
 */
final class AnalyzeCommand extends Command
{
    protected $signature = 'codeatlas:analyze
        {--analyzer=* : Only run the named analyzers (e.g. --analyzer=routes)}
        {--output= : Override the output directory}
        {--compact : Disable pretty-printing}';

    protected $description = 'Analyze this application and export the architecture graph as JSON';

    public function handle(): int
    {
        $basePath = $this->laravel->basePath();
        $outputDir = $this->outputDirectory();

        $this->components->info("Analyzing {$basePath}");

        ['runner' => $runner] = CodeAtlasFactory::make();

        /** @var list<string> $analyzerOption */
        $analyzerOption = (array) $this->option('analyzer');
        $filter = $analyzerOption === [] ? null : $analyzerOption;

        try {
            $result = $runner->run(
                projectPath: $basePath,
                scanConfig: $this->scanConfig(),
                analyzerFilter: $filter,
                exporters: [JsonExporter::class],
                exportConfig: new ExportConfig(
                    prettyPrint: $this->option('compact') ? false : (bool) config('codeatlas.pretty', true),
                ),
            );
        } catch (Throwable $e) {
            $this->components->error("Analysis failed: {$e->getMessage()}");

            return self::FAILURE;
        }

        $written = AnalysisWriter::write($result, $outputDir);

        $this->components->twoColumnDetail('Framework', $result->context->framework . ' ' . ($result->context->frameworkVersion ?? ''));
        $this->components->twoColumnDetail('Files scanned', (string) $result->context->fileCount());
        $this->components->twoColumnDetail('Analyzers', implode(', ', $result->analyzerNames()));
        $this->components->twoColumnDetail('Graph', $result->graph->nodeCount() . ' nodes, ' . $result->graph->edgeCount() . ' edges');
        $this->components->twoColumnDetail('Errors', (string) $result->errorCount());
        $this->components->twoColumnDetail('Duration', $result->durationMs . ' ms');

        foreach ($written as $path) {
            $this->components->twoColumnDetail('Written', $path);
        }

        return $result->errorCount() > 0 ? self::INVALID : self::SUCCESS;
    }

    private function outputDirectory(): string
    {
        /** @var string|null $override */
        $override = $this->option('output');

        if (is_string($override) && $override !== '') {
            return $override;
        }

        /** @var string $configured */
        $configured = config('codeatlas.output_path', storage_path('codeatlas'));

        return $configured;
    }

    private function scanConfig(): ?ScanConfig
    {
        /** @var list<string>|null $paths */
        $paths = config('codeatlas.scan_paths');
        /** @var list<string>|null $exclude */
        $exclude = config('codeatlas.exclude');

        if ($paths === null && $exclude === null) {
            return null;
        }

        $default = ScanConfig::default();

        return new ScanConfig(
            paths: $paths ?? $default->paths,
            excludePatterns: $exclude ?? $default->excludePatterns,
            fileExtensions: $default->fileExtensions,
        );
    }
}
