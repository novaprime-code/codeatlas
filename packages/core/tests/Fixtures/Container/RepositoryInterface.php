<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Container;

interface RepositoryInterface
{
    public function find(int $id): string;
}
