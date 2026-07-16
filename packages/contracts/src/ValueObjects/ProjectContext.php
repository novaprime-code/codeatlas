<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\ValueObjects;

use CodeAtlas\Contracts\Enums\FileType;

/**
 * Snapshot of a scanned project. Consumed by every analyzer.
 */
final readonly class ProjectContext
{
    /**
     * @param list<FileReference> $files
     * @param array<string, string> $autoloadPsr4 FQCN prefix => project-relative path
     */
    public function __construct(
        public string $name,
        public string $path,
        public string $framework,
        public ?string $frameworkVersion,
        public ?string $phpVersion,
        public array $files,
        public array $autoloadPsr4 = [],
    ) {}

    /**
     * Return only files matching the given type.
     *
     * @return list<FileReference>
     */
    public function filesOfType(FileType $type): array
    {
        return array_values(array_filter(
            $this->files,
            static fn(FileReference $file): bool => $file->type === $type,
        ));
    }

    public function fileCount(): int
    {
        return count($this->files);
    }

    /**
     * @return array<string, int> Map of type value => file count
     */
    public function fileCounts(): array
    {
        $counts = [];

        foreach ($this->files as $file) {
            $key = $file->type->value;
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        return $counts;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'path' => $this->path,
            'framework' => $this->framework,
            'framework_version' => $this->frameworkVersion,
            'php_version' => $this->phpVersion,
            'autoload_psr4' => $this->autoloadPsr4,
            'file_counts' => $this->fileCounts(),
            'files' => array_map(
                static fn(FileReference $file): array => $file->toArray(),
                $this->files,
            ),
        ];
    }
}
