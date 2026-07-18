<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Logging;

final class StderrSink implements LoggerSink
{
    public function write(string $line): void
    {
        fwrite(STDERR, $line);
    }
}
