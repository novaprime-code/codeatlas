<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Exceptions;

final class ScannerException extends CodeAtlasException
{
    public static function pathNotFound(string $path): self
    {
        return new self("Project path does not exist: {$path}");
    }

    public static function pathNotReadable(string $path): self
    {
        return new self("Project path is not readable: {$path}");
    }

    public static function pathNotDirectory(string $path): self
    {
        return new self("Project path is not a directory: {$path}");
    }

    public static function composerNotReadable(string $path): self
    {
        return new self("composer.json is not readable: {$path}");
    }

    public static function composerInvalidJson(string $path, string $reason): self
    {
        return new self("composer.json is not valid JSON ({$path}): {$reason}");
    }
}
