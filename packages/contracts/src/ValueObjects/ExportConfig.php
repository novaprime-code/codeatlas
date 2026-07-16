<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\ValueObjects;

/**
 * Configuration passed to an exporter.
 *
 * @phpstan-type ExportOptions array<string, scalar|array<string, mixed>|null>
 */
final readonly class ExportConfig
{
    /**
     * @param ExportOptions $options
     */
    public function __construct(
        public bool $prettyPrint = true,
        public ?string $outputPath = null,

        /** @var ExportOptions */
        public array $options = [],
    ) {}

    public static function default(): self
    {
        return new self();
    }
}
