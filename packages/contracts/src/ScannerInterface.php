<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts;

use CodeAtlas\Contracts\Exceptions\ScannerException;
use CodeAtlas\Contracts\ValueObjects\ProjectContext;
use CodeAtlas\Contracts\ValueObjects\ScanConfig;

/**
 * Discovers the analyzable files of a project.
 *
 * A scanner walks the filesystem, classifies files by type, and returns a
 * ProjectContext. It performs discovery only — never parsing.
 */
interface ScannerInterface
{
    /**
     * @param string $path Absolute path to the project root
     *
     * @throws ScannerException When the path does not exist or is unreadable
     */
    public function scan(string $path, ?ScanConfig $config = null): ProjectContext;
}
