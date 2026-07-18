<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Container;

final readonly class CircularA
{
    public function __construct(public CircularB $b) {}
}
