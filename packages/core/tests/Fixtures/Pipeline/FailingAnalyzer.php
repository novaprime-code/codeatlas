<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Pipeline;

use CodeAtlas\Contracts\AnalyzerInterface;
use CodeAtlas\Contracts\Enums\NodeType;
use CodeAtlas\Contracts\Exceptions\AnalyzerException;
use CodeAtlas\Contracts\ValueObjects\AnalysisResult;
use CodeAtlas\Contracts\ValueObjects\ProjectContext;

final class FailingAnalyzer implements AnalyzerInterface
{
    public function name(): string
    {
        return 'failing';
    }

    public function supportedNodeTypes(): array
    {
        return [NodeType::Controller];
    }

    public function analyze(ProjectContext $context): AnalysisResult
    {
        throw AnalyzerException::missingDependency($this->name(), 'a required contract');
    }
}
