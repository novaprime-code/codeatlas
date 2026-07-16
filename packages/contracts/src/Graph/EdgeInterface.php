<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Graph;

use CodeAtlas\Contracts\Enums\EdgeType;

/**
 * A directed relationship between two graph nodes.
 */
interface EdgeInterface
{
    /**
     * Globally unique, deterministic ID.
     */
    public function id(): string;

    /**
     * Source node ID.
     */
    public function source(): string;

    /**
     * Target node ID.
     */
    public function target(): string;

    public function type(): EdgeType;

    public function label(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
