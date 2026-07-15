<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Exceptions;

final class AnalyzerException extends CodeAtlasException
{
    public static function invalidConfiguration(string $analyzer, string $reason): self
    {
        return new self("Analyzer '{$analyzer}' has invalid configuration: {$reason}");
    }

    public static function missingDependency(string $analyzer, string $dependency): self
    {
        return new self("Analyzer '{$analyzer}' is missing required dependency: {$dependency}");
    }
}
