<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Exceptions;

final class ConfigurationException extends CodeAtlasException
{
    public static function missingKey(string $key): self
    {
        return new self("Configuration key is missing: {$key}");
    }

    public static function invalidValue(string $key, string $reason): self
    {
        return new self("Configuration key '{$key}' has an invalid value: {$reason}");
    }

    public static function fileNotReadable(string $path): self
    {
        return new self("Configuration file is not readable: {$path}");
    }
}
