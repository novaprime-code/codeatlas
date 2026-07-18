<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Container;

use Closure;

/**
 * Internal container binding record.
 */
final readonly class Binding
{
    public function __construct(
        public Closure|string $concrete,
        public bool $shared,
    ) {}
}
