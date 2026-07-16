<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Exceptions;

use Throwable;

final class ParserException extends CodeAtlasException
{
    public static function fileNotReadable(string $path): self
    {
        return new self("File is not readable: {$path}");
    }

    public static function syntaxError(string $path, string $message, ?Throwable $previous = null): self
    {
        return new self("Syntax error in {$path}: {$message}", 0, $previous);
    }
}
