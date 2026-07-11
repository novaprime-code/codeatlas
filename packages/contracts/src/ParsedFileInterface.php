<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts;

/**
 * The result of parsing a single PHP file.
 */
interface ParsedFileInterface
{
    public function path(): string;

    public function namespace(): ?string;

    /**
     * Import statements of the file.
     *
     * @return array<string, string> Map of alias => fully qualified class name
     */
    public function useStatements(): array;

    /**
     * Fully qualified names of all class-like declarations
     * (classes, interfaces, traits, enums) in the file.
     *
     * @return list<string>
     */
    public function classNames(): array;
}
