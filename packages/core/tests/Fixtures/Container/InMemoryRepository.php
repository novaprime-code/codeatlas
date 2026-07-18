<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Container;

final class InMemoryRepository implements RepositoryInterface
{
    public function find(int $id): string
    {
        return "record-{$id}";
    }
}
