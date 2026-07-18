<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Container;

final readonly class UnresolvableService
{
    public function __construct(public string $someString) {}
}
