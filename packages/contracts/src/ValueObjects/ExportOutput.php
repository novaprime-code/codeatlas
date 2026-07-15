<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\ValueObjects;

/**
 * The result of an exporter run.
 *
 * "content" holds the exported payload as a string (JSON, Mermaid, etc.).
 * "mimeType" and "filename" are hints for consumers writing to disk or serving.
 */
final readonly class ExportOutput
{
    public function __construct(
        public string $content,
        public string $mimeType,
        public string $filename,
    ) {}

    public function byteCount(): int
    {
        return strlen($this->content);
    }
}
