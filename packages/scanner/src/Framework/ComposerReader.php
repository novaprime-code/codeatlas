<?php

declare(strict_types=1);

namespace CodeAtlas\Scanner\Framework;

use CodeAtlas\Contracts\Exceptions\ScannerException;
use JsonException;

/**
 * Read metadata from a project's composer.json.
 *
 * Extracts the fields the pipeline needs: project name, PHP requirement,
 * declared dependencies (for framework detection), and PSR-4 autoload
 * mappings (for analyzer FQCN resolution).
 *
 * A missing file returns null via tryRead(); a malformed file throws.
 * This split lets scanners tolerate projects without composer.json while
 * still surfacing real corruption via the contracts-level ScannerException.
 */
final class ComposerReader
{
    public function tryRead(string $projectPath): ?ComposerMetadata
    {
        $path = $this->composerPath($projectPath);

        if (!is_file($path)) {
            return null;
        }

        return $this->read($projectPath);
    }

    /**
     * @throws ScannerException
     */
    public function read(string $projectPath): ComposerMetadata
    {
        $path = $this->composerPath($projectPath);

        if (!is_readable($path)) {
            throw ScannerException::composerNotReadable($path);
        }

        $raw = @file_get_contents($path);

        if ($raw === false) {
            throw ScannerException::composerNotReadable($path);
        }

        try {
            /** @var mixed $decoded */
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw ScannerException::composerInvalidJson($path, $e->getMessage());
        }

        if (!is_array($decoded)) {
            throw ScannerException::composerInvalidJson($path, 'expected JSON object at root');
        }

        return ComposerMetadata::fromArray($decoded);
    }

    private function composerPath(string $projectPath): string
    {
        return rtrim($projectPath, "/\\") . '/composer.json';
    }
}
