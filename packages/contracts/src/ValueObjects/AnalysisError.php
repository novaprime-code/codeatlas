<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\ValueObjects;

use CodeAtlas\Contracts\Enums\Severity;

/**
 * A non-fatal issue encountered during analysis.
 *
 * Analyzers use these to report per-file failures (parse errors,
 * missing dependencies, etc.) without aborting the full analysis.
 */
final readonly class AnalysisError
{
    public function __construct(
        public string $analyzer,
        public Severity $severity,
        public string $message,
        public ?string $file = null,
        public ?int $line = null,
        public ?string $exception = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'analyzer' => $this->analyzer,
            'severity' => $this->severity->value,
            'message' => $this->message,
            'file' => $this->file,
            'line' => $this->line,
            'exception' => $this->exception,
        ];
    }
}
