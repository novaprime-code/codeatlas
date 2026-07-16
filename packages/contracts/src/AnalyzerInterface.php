<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts;

use CodeAtlas\Contracts\Enums\NodeType;
use CodeAtlas\Contracts\Exceptions\AnalyzerException;
use CodeAtlas\Contracts\ValueObjects\AnalysisResult;
use CodeAtlas\Contracts\ValueObjects\ProjectContext;

/**
 * The contract every CodeAtlas analyzer implements.
 *
 * An analyzer receives a ProjectContext (the discovered files of a project),
 * inspects the files relevant to its domain, and returns an AnalysisResult
 * containing graph nodes and edges.
 *
 * Analyzers MUST be stateless, MUST NOT import from other analyzers, and
 * MUST handle malformed input files gracefully (log, skip, continue).
 */
interface AnalyzerInterface
{
    /**
     * Unique machine name of this analyzer (e.g. "routes", "controllers").
     */
    public function name(): string;

    /**
     * The node types this analyzer produces.
     *
     * @return list<NodeType>
     */
    public function supportedNodeTypes(): array;

    /**
     * Run the analysis against a project.
     *
     * Implementations must not throw for per-file parse failures; those are
     * collected in AnalysisResult::$errors. AnalyzerException is reserved
     * for unrecoverable conditions (e.g. invalid configuration).
     *
     * @throws AnalyzerException
     */
    public function analyze(ProjectContext $context): AnalysisResult;
}
