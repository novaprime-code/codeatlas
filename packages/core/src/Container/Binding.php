<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Container;

/**
 * Internal container binding record.
 */
final readonly class Binding
{
    /**
     * @param class-string|callable $concrete
     */
    public function __construct(
        public string|object $concrete,
        public bool $shared,
    ) {}
}
