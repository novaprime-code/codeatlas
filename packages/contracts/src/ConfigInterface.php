<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts;

/**
 * Read access to CodeAtlas configuration with dot-notation keys.
 */
interface ConfigInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function has(string $key): bool;

    /**
     * @return array<string, mixed>
     */
    public function all(): array;
}
