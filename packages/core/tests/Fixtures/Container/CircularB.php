<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Container;

final readonly class CircularB
{
    public function __construct(public CircularA $a) {}
}
