<?php

declare(strict_types=1);

namespace CodeAtlas\Analyzers\Routes\Extraction;

final readonly class ActionResult
{
    public function __construct(
        public ?string $controller,
        public ?string $action,
        public bool $isClosure,
    ) {}
}
