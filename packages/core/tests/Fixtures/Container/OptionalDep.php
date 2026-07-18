<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Container;

final readonly class OptionalDep
{
    public function __construct(public ?SimpleService $simple = null, public int $count = 42) {}
}
