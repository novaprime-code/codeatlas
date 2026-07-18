<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Container;

final class SimpleService
{
    public function ping(): string
    {
        return 'pong';
    }
}
