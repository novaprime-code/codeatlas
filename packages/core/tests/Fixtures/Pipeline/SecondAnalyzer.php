<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Pipeline;

use CodeAtlas\Contracts\AnalyzerInterface;
use CodeAtlas\Contracts\Enums\NodeType;
use CodeAtlas\Contracts\Graph\Node;
use CodeAtlas\Contracts\ValueObjects\AnalysisResult;
use CodeAtlas\Contracts\ValueObjects\ProjectContext;

final class SecondAnalyzer implements AnalyzerInterface
{
    public function name(): string
    {
        return 'second';
    }

    public function supportedNodeTypes(): array
    {
        return [NodeType::Model];
    }

    public function analyze(ProjectContext $context): AnalysisResult
    {
        return new AnalysisResult(
            analyzer: $this->name(),
            nodes: [Node::make(NodeType::Model, 'App\\User', 'User')],
        );
    }
}
