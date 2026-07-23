<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Pipeline;

use CodeAtlas\Contracts\AnalyzerInterface;
use CodeAtlas\Contracts\ContainerInterface;
use CodeAtlas\Contracts\Enums\Severity;
use CodeAtlas\Contracts\Exceptions\AnalyzerException;
use CodeAtlas\Contracts\ExporterInterface;
use CodeAtlas\Contracts\Graph\Graph;
use CodeAtlas\Contracts\ScannerInterface;
use CodeAtlas\Contracts\ValueObjects\AnalysisError;
use CodeAtlas\Contracts\ValueObjects\AnalysisResult;
use CodeAtlas\Contracts\ValueObjects\ExportConfig;
use CodeAtlas\Contracts\ValueObjects\ExportOutput;
use CodeAtlas\Contracts\ValueObjects\ProjectContext;
use CodeAtlas\Contracts\ValueObjects\ScanConfig;
use CodeAtlas\Core\Events\EventBus;
use CodeAtlas\Core\Events\Events;
use CodeAtlas\Core\Plugin\PluginLoader;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

/**
 * Orchestrates the CodeAtlas pipeline: Scanner → Analyzers → Exporters.
 *
 * The runner is the single place where framework-agnostic components
 * are composed. Each stage is observable via EventBus and each analyzer
 * runs in isolation — an analyzer that throws AnalyzerException is
 * recorded as an error against the final result and the pipeline
 * continues with the remaining analyzers.
 *
 * Per the constitution: JSON is the only contract between backend and
 * frontend. This runner produces `PipelineResult`, which carries the
 * merged Graph, per-analyzer results, and metadata that the JSON
 * exporter serializes.
 */
final class PipelineRunner
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly ScannerInterface $scanner,
        private readonly EventBus $events,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    /**
     * Run the full pipeline against a project path.
     *
     * @param list<string>|null $analyzerFilter Machine names to include; null = all
     * @param list<class-string<ExporterInterface>> $exporters Exporters to invoke
     *
     * @throws \CodeAtlas\Contracts\Exceptions\ScannerException when the project path is missing, not a directory, or unreadable
     */
    public function run(
        string $projectPath,
        ?ScanConfig $scanConfig = null,
        ?array $analyzerFilter = null,
        array $exporters = [],
        ?ExportConfig $exportConfig = null,
    ): PipelineResult {
        $startedAt = microtime(true);
        $this->events->dispatch(Events::PIPELINE_STARTED, $projectPath);

        $context = $this->scan($projectPath, $scanConfig);
        $results = $this->analyze($context, $analyzerFilter);
        $graph = $this->mergeIntoGraph($results);

        $durationSoFar = (int) round((microtime(true) - $startedAt) * 1000);
        $enrichedConfig = $this->enrichExportConfig($exportConfig ?? ExportConfig::default(), $context, $durationSoFar);
        $exports = $this->export($results, $graph, $exporters, $enrichedConfig);

        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
        $pipeline = new PipelineResult($context, $results, $graph, $exports, $durationMs);

        $this->events->dispatch(Events::PIPELINE_COMPLETED, $pipeline);

        return $pipeline;
    }

    /**
     * @throws \CodeAtlas\Contracts\Exceptions\ScannerException
     */
    private function scan(string $path, ?ScanConfig $config): ProjectContext
    {
        $this->events->dispatch(Events::SCAN_STARTED, $path);
        $context = $this->scanner->scan($path, $config);
        $this->events->dispatch(Events::SCAN_COMPLETED, $context);

        return $context;
    }

    /**
     * @param list<string>|null $filter
     *
     * @return list<AnalysisResult>
     */
    private function analyze(ProjectContext $context, ?array $filter): array
    {
        /** @var list<AnalyzerInterface> $analyzers */
        $analyzers = $this->container->tagged(PluginLoader::TAG_ANALYZER);
        $results = [];

        foreach ($analyzers as $analyzer) {
            $name = $analyzer->name();

            if ($filter !== null && !in_array($name, $filter, true)) {
                continue;
            }

            $this->events->dispatch(Events::ANALYSIS_STARTED, $name);

            try {
                $results[] = $analyzer->analyze($context);
                $this->events->dispatch(Events::ANALYSIS_COMPLETED, $name);
            } catch (AnalyzerException $e) {
                $this->logger->error('Analyzer {name} failed: {message}', [
                    'name' => $name,
                    'message' => $e->getMessage(),
                ]);
                $this->events->dispatch(Events::ANALYSIS_ERROR, $name);
                $results[] = new AnalysisResult(
                    analyzer: $name,
                    errors: [new AnalysisError($name, Severity::Error, $e->getMessage(), exception: $e::class)],
                );
            } catch (Throwable $e) {
                $this->logger->critical('Analyzer {name} crashed: {message}', [
                    'name' => $name,
                    'message' => $e->getMessage(),
                ]);
                $this->events->dispatch(Events::ANALYSIS_ERROR, $name);
                $results[] = new AnalysisResult(
                    analyzer: $name,
                    errors: [new AnalysisError($name, Severity::Error, 'Uncaught: ' . $e->getMessage(), exception: $e::class)],
                );
            }
        }

        return $results;
    }

    /**
     * @param list<AnalysisResult> $results
     */
    private function mergeIntoGraph(array $results): Graph
    {
        $graph = new Graph();

        foreach ($results as $result) {
            foreach ($result->nodes as $node) {
                $graph->addNode($node);
            }
            foreach ($result->edges as $edge) {
                $graph->addEdge($edge);
            }
        }

        return $graph;
    }

    /**
     * Inject project metadata and elapsed duration into the export config.
     *
     * Exporters only receive an AnalysisResult, but the JSON schema's
     * project block needs ProjectContext data — which exists only after
     * scanning. The runner is the single component holding both, so it
     * merges them here. Caller-provided options always win on conflict.
     */
    private function enrichExportConfig(ExportConfig $config, ProjectContext $context, int $durationMs): ExportConfig
    {
        $projectOptions = [
            'project' => [
                'name' => $context->name,
                'path' => $context->path,
                'framework' => $context->framework,
                'framework_version' => $context->frameworkVersion,
                'php_version' => $context->phpVersion,
            ],
            'duration_ms' => $durationMs,
        ];

        return new ExportConfig(
            prettyPrint: $config->prettyPrint,
            outputPath: $config->outputPath,
            options: array_merge($projectOptions, $config->options),
        );
    }

    /**
     * @param list<AnalysisResult> $results
     * @param list<class-string<ExporterInterface>> $exporterClasses
     *
     * @return array<string, ExportOutput>
     */
    private function export(array $results, Graph $graph, array $exporterClasses, ExportConfig $exportConfig): array
    {
        if ($exporterClasses === []) {
            return [];
        }

        $merged = $this->mergeResultsForExport($results);
        $outputs = [];

        foreach ($exporterClasses as $exporterClass) {
            /** @var ExporterInterface $exporter */
            $exporter = $this->container->make($exporterClass);
            $name = $exporter->name();
            $this->events->dispatch(Events::EXPORT_STARTED, $name);

            $outputs[$name] = $exporter->export($merged, $exportConfig);

            $this->events->dispatch(Events::EXPORT_COMPLETED, $name);
        }

        return $outputs;
    }

    /**
     * Fold per-analyzer results into a single AnalysisResult labelled 'pipeline'
     * for exporters that want a monolithic view.
     *
     * @param list<AnalysisResult> $results
     */
    private function mergeResultsForExport(array $results): AnalysisResult
    {
        $merged = new AnalysisResult(analyzer: 'pipeline');

        foreach ($results as $result) {
            $merged = $merged->merge(new AnalysisResult(
                analyzer: 'pipeline',
                nodes: $result->nodes,
                edges: $result->edges,
                metadata: [$result->analyzer => $result->metadata],
                errors: $result->errors,
            ));
        }

        return $merged;
    }
}
