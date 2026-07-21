<?php

declare(strict_types=1);

namespace CodeAtlas\Scanner\Framework;

final readonly class FrameworkResult
{
    public function __construct(
        public string $framework,
        public ?string $version,
    ) {}

    public function isKnown(): bool
    {
        return $this->framework !== FrameworkDetector::FRAMEWORK_UNKNOWN;
    }
}
