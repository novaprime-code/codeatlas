<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\ValueObjects;

/**
 * Configuration for a scanner run.
 */
final readonly class ScanConfig
{
    /**
     * @param list<string> $paths Project-relative directories to scan
     * @param list<string> $excludePatterns Glob patterns to exclude
     * @param list<string> $fileExtensions File extensions to include (with leading dot)
     */
    public function __construct(
        public array $paths = ['app', 'routes', 'config', 'database', 'bootstrap', 'resources'],
        public array $excludePatterns = ['vendor', 'node_modules', 'storage', '.git', 'tests','public'],
        public array $fileExtensions = ['.php'],
    ) {}

    public static function default(): self
    {
        return new self();
    }

    public function withPaths(string ...$paths): self
    {
        return new self(
            paths: array_values($paths),
            excludePatterns: $this->excludePatterns,
            fileExtensions: $this->fileExtensions,
        );
    }

    public function withExclusions(string ...$patterns): self
    {
        return new self(
            paths: $this->paths,
            excludePatterns: array_values($patterns),
            fileExtensions: $this->fileExtensions,
        );
    }
}
