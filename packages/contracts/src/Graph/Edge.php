<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Graph;

use CodeAtlas\Contracts\Enums\EdgeType;
use InvalidArgumentException;

/**
 * Default immutable edge implementation.
 */
final readonly class Edge implements EdgeInterface
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private string $id,
        private string $source,
        private string $target,
        private EdgeType $type,
        private ?string $label = null,
        private array $metadata = [],
    ) {
        if ($this->id === '' || $this->source === '' || $this->target === '') {
            throw new InvalidArgumentException('Edge ID, source, and target must not be empty.');
        }
    }

    /**
     * Named constructor building a deterministic ID from the endpoints.
     *
     * @param array<string, mixed> $metadata
     */
    public static function make(
        string $source,
        string $target,
        EdgeType $type,
        ?string $label = null,
        array $metadata = [],
    ): self {
        return new self(
            id: sprintf('edge::%s::%s->%s', $type->value, $source, $target),
            source: $source,
            target: $target,
            type: $type,
            label: $label,
            metadata: $metadata,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;
        $source = $data['source'] ?? null;
        $target = $data['target'] ?? null;
        $type = $data['type'] ?? null;

        if (!is_string($id) || !is_string($source) || !is_string($target) || !is_string($type)) {
            throw new InvalidArgumentException('Edge array requires string keys: id, source, target, type.');
        }

        $label = $data['label'] ?? null;
        $metadata = $data['metadata'] ?? [];

        return new self(
            id: $id,
            source: $source,
            target: $target,
            type: EdgeType::from($type),
            label: is_string($label) ? $label : null,
            metadata: is_array($metadata) ? $metadata : [],
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function target(): string
    {
        return $this->target;
    }

    public function type(): EdgeType
    {
        return $this->type;
    }

    public function label(): ?string
    {
        return $this->label;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'source' => $this->source,
            'target' => $this->target,
            'type' => $this->type->value,
            'label' => $this->label,
            'metadata' => $this->metadata,
        ];
    }
}
