<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Graph;

use CodeAtlas\Contracts\Enums\NodeType;
use CodeAtlas\Contracts\ValueObjects\FileReference;

/**
 * A single entity in the architecture graph.
 */
interface NodeInterface
{
    /**
     * Globally unique, deterministic ID in the form "{type}::{qualifier}".
     */
    public function id(): string;

    public function type(): NodeType;

    /**
     * Human-readable label shown in the UI.
     */
    public function label(): string;

    /**
     * Optional visual/logical grouping key (e.g. "api", "web").
     */
    public function group(): ?string;

    public function file(): ?FileReference;

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array;

    /**
     * @return list<string>
     */
    public function tags(): array;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
