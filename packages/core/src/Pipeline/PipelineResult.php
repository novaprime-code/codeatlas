<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Pipeline;

use CodeAtlas\Contracts\Graph\Graph;
use CodeAtlas\Contracts\ValueObjects\AnalysisResult;
use CodeAtlas\Contracts\ValueObjects\ExportOutput;
use CodeAtlas\Contracts\ValueObjects\ProjectContext;

/**
 * Complete output of one pipeline run.
 *
 * Carries the discovered project context, every per-analyzer result,
 * the merged Graph, all exporter outputs indexed by exporter name,
 * and the total wall-clock duration.
 */
final readonly class PipelineResult
{
    /**
     * @param list<AnalysisResult> $results
     * @param array<string, ExportOutput> $exports
     */
    public function __construct(
        public ProjectContext $context,
        public array $results,
        public Graph $graph,
        public array $exports,
        public int $durationMs,
    ) {}

    public function errorCount(): int
    {
        $count = 0;

        foreach ($this->results as $result) {
            $count += count($result->errors);
        }

        return $count;
    }

    public function analyzerNames(): array
    {
        return array_map(
            static fn (AnalysisResult $r): string => $r->analyzer,
            $this->results,
        );
    }
}
