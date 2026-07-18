<?php

declare(strict_types=1);

namespace CodeAtlas\Scanner;

use CodeAtlas\Contracts\Exceptions\ScannerException;
use CodeAtlas\Contracts\ScannerInterface;
use CodeAtlas\Contracts\ValueObjects\FileReference;
use CodeAtlas\Contracts\ValueObjects\ProjectContext;
use CodeAtlas\Contracts\ValueObjects\ScanConfig;
use CodeAtlas\Scanner\Classification\FileClassifier;
use CodeAtlas\Scanner\Discovery\DirectoryWalker;
use CodeAtlas\Scanner\Framework\ComposerReader;
use CodeAtlas\Scanner\Framework\FrameworkDetector;

/**
 * Framework-agnostic file discovery.
 *
 * Given a project path, the scanner:
 *   1. Validates the path exists and is a readable directory
 *   2. Reads composer.json (if present) for metadata
 *   3. Detects the framework (Laravel today; more later)
 *   4. Walks the configured scan paths, classifying each file
 *   5. Returns a fully populated ProjectContext
 *
 * The scanner never parses PHP — that's the analyzer's job. Its only
 * output is metadata: paths, types, framework identification, autoload
 * mappings. Everything analyzers need to know before reading source code.
 */
final class Scanner implements ScannerInterface
{
    public function __construct(
        private readonly DirectoryWalker $walker,
        private readonly ComposerReader $composerReader,
        private readonly FrameworkDetector $frameworkDetector,
    ) {}

    public static function default(): self
    {
        return new self(
            walker: new DirectoryWalker(new FileClassifier()),
            composerReader: new ComposerReader(),
            frameworkDetector: new FrameworkDetector(),
        );
    }

    public function scan(string $path, ?ScanConfig $config = null): ProjectContext
    {
        $normalized = $this->validatePath($path);
        $config ??= ScanConfig::default();

        $composer = $this->composerReader->tryRead($normalized);
        $framework = $this->frameworkDetector->detect($normalized, $composer);

        /** @var list<FileReference> $files */
        $files = iterator_to_array($this->walker->walk($normalized, $config), false);

        return new ProjectContext(
            name: $composer?->name ?? basename($normalized),
            path: $normalized,
            framework: $framework->framework,
            frameworkVersion: $framework->version,
            phpVersion: $composer?->phpRequirement,
            files: $files,
            autoloadPsr4: $composer?->autoloadPsr4 ?? [],
        );
    }

    /**
     * @throws ScannerException
     */
    private function validatePath(string $path): string
    {
        if (!file_exists($path)) {
            throw ScannerException::pathNotFound($path);
        }

        if (!is_dir($path)) {
            throw ScannerException::pathNotDirectory($path);
        }

        if (!is_readable($path)) {
            throw ScannerException::pathNotReadable($path);
        }

        return rtrim(str_replace('\\', '/', $path), '/');
    }
}
