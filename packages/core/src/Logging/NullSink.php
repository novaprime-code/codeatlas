<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Logging;

/**
 * Silent sink — useful for tests and when logging is explicitly disabled.
 */
final class NullSink implements LoggerSink
{
    /** @var list<string> */
    public array $lines = [];

    public function write(string $line): void
    {
        $this->lines[] = $line;
    }
}
