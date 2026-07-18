<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Pipeline;

use CodeAtlas\Contracts\AnalyzerInterface;
use CodeAtlas\Contracts\Enums\EdgeType;
use CodeAtlas\Contracts\Enums\NodeType;
use CodeAtlas\Contracts\Graph\Edge;
use CodeAtlas\Contracts\Graph\Node;
use CodeAtlas\Contracts\ValueObjects\AnalysisResult;
use CodeAtlas\Contracts\ValueObjects\ProjectContext;

final class WorkingAnalyzer implements AnalyzerInterface
{
    public function name(): string
    {
        return 'working';
    }

    public function supportedNodeTypes(): array
    {
        return [NodeType::Route];
    }

    public function analyze(ProjectContext $context): AnalysisResult
    {
        return new AnalysisResult(
            analyzer: $this->name(),
            nodes: [Node::make(NodeType::Route, 'get::/api/x', 'GET /api/x')],
            edges: [Edge::make('route::get::/api/x', 'controller::App\\Foo', EdgeType::RoutesTo)],
            metadata: ['files_analyzed' => 1],
        );
    }
}
