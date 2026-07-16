<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Logging;

interface LoggerSink
{
    public function write(string $line): void;
}
