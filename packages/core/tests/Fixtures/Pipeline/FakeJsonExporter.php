<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Pipeline;

use CodeAtlas\Contracts\ExporterInterface;
use CodeAtlas\Contracts\ValueObjects\AnalysisResult;
use CodeAtlas\Contracts\ValueObjects\ExportConfig;
use CodeAtlas\Contracts\ValueObjects\ExportOutput;

final class FakeJsonExporter implements ExporterInterface
{
    public function name(): string { return 'json'; }

    public function export(AnalysisResult $result, ExportConfig $config): ExportOutput
    {
        $content = (string) json_encode([
            'nodes' => count($result->nodes),
            'edges' => count($result->edges),
            'errors' => count($result->errors),
        ]);

        return new ExportOutput($content, 'application/json', 'analysis.json');
    }
}
