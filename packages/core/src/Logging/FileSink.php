<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Logging;

use RuntimeException;

final class FileSink implements LoggerSink
{
    /** @var resource|null */
    private $handle = null;

    public function __construct(private readonly string $path) {}

    public function write(string $line): void
    {
        if ($this->handle === null) {
            $handle = @fopen($this->path, 'a');
            if ($handle === false) {
                throw new RuntimeException("Cannot open log file: {$this->path}");
            }
            $this->handle = $handle;
        }

        fwrite($this->handle, $line);
    }

    public function __destruct()
    {
        if ($this->handle !== null) {
            fclose($this->handle);
        }
    }
}
