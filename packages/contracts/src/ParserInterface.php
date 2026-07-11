<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts;

use CodeAtlas\Contracts\Exceptions\ParserException;

/**
 * AST parsing abstraction.
 *
 * The concrete implementation (codeatlas/core) wraps nikic/php-parser and
 * exposes richer helpers; this contract defines the minimal surface that
 * framework-agnostic consumers rely on.
 */
interface ParserInterface
{
    /**
     * Parse a PHP file from disk.
     *
     * @throws ParserException When the file is unreadable or contains syntax errors
     */
    public function parse(string $filePath): ParsedFileInterface;

    /**
     * Parse PHP code from a string.
     *
     * @throws ParserException When the code contains syntax errors
     */
    public function parseString(string $code, string $virtualPath = 'inline://code'): ParsedFileInterface;
}
