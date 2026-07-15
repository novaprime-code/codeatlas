<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Exceptions;

use Throwable;

final class ExporterException extends CodeAtlasException
{
    public static function writeFailed(string $path, ?Throwable $previous = null): self
    {
        return new self("Failed to write export output to: {$path}", 0, $previous);
    }

    public static function encodingFailed(string $format, string $reason): self
    {
        return new self("Failed to encode as {$format}: {$reason}");
    }
}
