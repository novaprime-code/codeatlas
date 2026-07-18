<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Parser;

use CodeAtlas\Contracts\Exceptions\ParserException;
use CodeAtlas\Contracts\ParsedFileInterface;
use CodeAtlas\Contracts\ParserInterface;
use PhpParser\ErrorHandler\Collecting;
use PhpParser\Parser;
use PhpParser\ParserFactory;

/**
 * ParserInterface implementation backed by nikic/php-parser v5.
 *
 * The parser never crashes the caller: syntax errors and unreadable files
 * are converted to typed ParserException instances. An in-memory AST cache
 * keyed by file-content hash prevents re-parsing when the same file is
 * requested repeatedly by multiple analyzers within a single pipeline run.
 *
 * The cache is intentionally per-instance and unbounded; analyzers share
 * a single parser instance registered as a singleton in the container.
 */
final class PhpParser implements ParserInterface
{
    private readonly Parser $parser;

    /** @var array<string, ParsedFile> */
    private array $cache = [];

    public function __construct(?Parser $parser = null)
    {
        $this->parser = $parser ?? (new ParserFactory())->createForNewestSupportedVersion();
    }

    public function parse(string $filePath): ParsedFileInterface
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw ParserException::fileNotReadable($filePath);
        }

        $source = @file_get_contents($filePath);

        if ($source === false) {
            throw ParserException::fileNotReadable($filePath);
        }

        $cacheKey = md5($filePath . "\0" . $source);

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $errorHandler = new Collecting();
        $ast = $this->parser->parse($source, $errorHandler);

        foreach ($errorHandler->getErrors() as $error) {
            throw ParserException::syntaxError($filePath, $error->getMessage(), $error);
        }

        $parsed = new ParsedFile($filePath, array_values($ast ?? []));
        $this->cache[$cacheKey] = $parsed;

        return $parsed;
    }

    public function parseString(string $code, string $virtualPath = 'inline://code'): ParsedFileInterface
    {
        $cacheKey = md5($virtualPath . "\0" . $code);

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $errorHandler = new Collecting();
        $ast = $this->parser->parse($code, $errorHandler);

        foreach ($errorHandler->getErrors() as $error) {
            throw ParserException::syntaxError($virtualPath, $error->getMessage(), $error);
        }

        $parsed = new ParsedFile($virtualPath, array_values($ast ?? []));
        $this->cache[$cacheKey] = $parsed;

        return $parsed;
    }

    public function cacheSize(): int
    {
        return count($this->cache);
    }

    public function clearCache(): void
    {
        $this->cache = [];
    }
}
