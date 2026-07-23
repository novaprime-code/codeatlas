<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Container;

final readonly class OptionalUnresolvableDep
{
    public function __construct(
        public ?RepositoryInterface $repository = null,
    ) {}
}
