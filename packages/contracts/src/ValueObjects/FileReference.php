<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\ValueObjects;

use CodeAtlas\Contracts\Enums\FileType;
use InvalidArgumentException;

/**
 * Reference to a discovered project file.
 *
 * "path" is always project-relative (e.g. "app/Http/Controllers/UserController.php").
 * "absolutePath" is the resolved filesystem path.
 */
final readonly class FileReference
{
    public function __construct(
        public string $path,
        public string $absolutePath,
        public FileType $type,
        public int $lineStart = 1,
        public ?int $lineEnd = null,
    ) {
        if ($this->path === '' || $this->absolutePath === '') {
            throw new InvalidArgumentException('FileReference path and absolutePath must not be empty.');
        }

        if ($this->lineStart < 1) {
            throw new InvalidArgumentException('FileReference lineStart must be >= 1.');
        }

        if ($this->lineEnd !== null && $this->lineEnd < $this->lineStart) {
            throw new InvalidArgumentException('FileReference lineEnd must be >= lineStart.');
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $path = $data['path'] ?? null;
        $absolutePath = $data['absolute_path'] ?? $path;
        $type = $data['type'] ?? FileType::Other->value;
        $lineStart = $data['line_start'] ?? 1;
        $lineEnd = $data['line_end'] ?? null;

        if (!is_string($path) || !is_string($absolutePath) || !is_string($type)) {
            throw new InvalidArgumentException('FileReference array requires string path and type.');
        }

        return new self(
            path: $path,
            absolutePath: $absolutePath,
            type: FileType::from($type),
            lineStart: is_int($lineStart) ? $lineStart : 1,
            lineEnd: is_int($lineEnd) ? $lineEnd : null,
        );
    }

    public function withLineRange(int $start, ?int $end = null): self
    {
        return new self(
            path: $this->path,
            absolutePath: $this->absolutePath,
            type: $this->type,
            lineStart: $start,
            lineEnd: $end,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'absolute_path' => $this->absolutePath,
            'type' => $this->type->value,
            'line_start' => $this->lineStart,
            'line_end' => $this->lineEnd,
        ];
    }
}
