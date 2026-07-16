<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts;

use CodeAtlas\Contracts\Exceptions\ExporterException;
use CodeAtlas\Contracts\ValueObjects\AnalysisResult;
use CodeAtlas\Contracts\ValueObjects\ExportConfig;
use CodeAtlas\Contracts\ValueObjects\ExportOutput;

/**
 * Converts an AnalysisResult into an output format (JSON, Mermaid, ...).
 */
interface ExporterInterface
{
    /**
     * Unique machine name of this exporter (e.g. "json", "mermaid").
     */
    public function name(): string;

    /**
     * @throws ExporterException
     */
    public function export(AnalysisResult $result, ExportConfig $config): ExportOutput;
}
