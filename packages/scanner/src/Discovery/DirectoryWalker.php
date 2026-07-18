<?php

declare(strict_types=1);

namespace CodeAtlas\Scanner\Discovery;

use CodeAtlas\Contracts\ValueObjects\FileReference;
use CodeAtlas\Contracts\ValueObjects\ScanConfig;
use CodeAtlas\Scanner\Classification\FileClassifier;
use Generator;
use Symfony\Component\Finder\Finder;

/**
 * Walk a project directory and yield classified FileReference records.
 *
 * The walker composes two decisions:
 *   1. WHERE to look — the configured scan paths, resolved against the
 *      project root, with excluded paths pruned
 *   2. WHAT to include — files matching the configured extensions
 *
 * Iteration is lazy — Symfony Finder yields SplFileInfo instances one at
 * a time, so a 5000-file project doesn't materialize as a giant array.
 * Callers that need a list can coerce via iterator_to_array().
 */
final class DirectoryWalker
{
    public function __construct(private readonly FileClassifier $classifier) {}

    /**
     * @return Generator<int, FileReference>
     */
    public function walk(string $projectPath, ScanConfig $config): Generator
    {
        $projectPath = $this->normalizeProjectPath($projectPath);

        foreach ($config->paths as $relativePath) {
            $absolute = $projectPath . '/' . ltrim($relativePath, '/');

            if (!is_dir($absolute)) {
                continue;
            }

            yield from $this->walkPath($projectPath, $absolute, $config);
        }
    }

    /**
     * @return Generator<int, FileReference>
     */
    private function walkPath(string $projectPath, string $absolute, ScanConfig $config): Generator
    {
        $finder = new Finder();
        $finder->files()
            ->in($absolute)
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);

        $exclusions = $this->filterExclusions($config->excludePatterns);

        if ($exclusions !== []) {
            $finder->exclude($exclusions);
        }

        $extensions = $config->fileExtensions;

        foreach ($finder as $file) {
            $absPath = $file->getRealPath();

            if ($absPath === false || !$this->extensionAllowed($absPath, $extensions)) {
                continue;
            }

            $relative = $this->relativize($projectPath, $absPath);

            if ($this->matchesAnyExclusion($relative, $config->excludePatterns)) {
                continue;
            }

            yield new FileReference(
                path: $relative,
                absolutePath: $absPath,
                type: $this->classifier->classify($relative),
            );
        }
    }

    /**
     * Symfony Finder's exclude() only accepts directory names (not glob
     * patterns). Drop patterns that look like globs; those are matched
     * later against the relative path via matchesAnyExclusion().
     *
     * @param list<string> $patterns
     *
     * @return list<string>
     */
    private function filterExclusions(array $patterns): array
    {
        $simple = [];

        foreach ($patterns as $pattern) {
            if (!str_contains($pattern, '*') && !str_contains($pattern, '?')) {
                $simple[] = trim($pattern, '/');
            }
        }

        return $simple;
    }

    /**
     * @param list<string> $patterns
     */
    private function matchesAnyExclusion(string $relative, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (!str_contains($pattern, '*') && !str_contains($pattern, '?')) {
                continue;
            }

            if (fnmatch($pattern, $relative)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<string> $extensions
     */
    private function extensionAllowed(string $absolutePath, array $extensions): bool
    {
        if ($extensions === []) {
            return true;
        }

        foreach ($extensions as $ext) {
            if (str_ends_with($absolutePath, $ext)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeProjectPath(string $projectPath): string
    {
        return rtrim(str_replace('\\', '/', $projectPath), '/');
    }

    private function relativize(string $projectPath, string $absolutePath): string
    {
        $normalized = str_replace('\\', '/', $absolutePath);
        $prefix = $projectPath . '/';

        if (str_starts_with($normalized, $prefix)) {
            return substr($normalized, strlen($prefix));
        }

        return $normalized;
    }
}
