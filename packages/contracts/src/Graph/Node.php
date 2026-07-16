<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Graph;

use CodeAtlas\Contracts\Enums\NodeType;
use CodeAtlas\Contracts\ValueObjects\FileReference;
use InvalidArgumentException;
use TypeError;
use ValueError;

/**
 * Default immutable node implementation.
 */
final readonly class Node implements NodeInterface
{
    /**
     * @param array<string, mixed> $metadata
     * @param list<string> $tags
     */
    public function __construct(
        private string $id,
        private NodeType $type,
        private string $label,
        private ?string $group = null,
        private ?FileReference $file = null,
        private array $metadata = [],
        private array $tags = [],
    ) {
        if ($this->id === '') {
            throw new InvalidArgumentException('Node ID must not be empty.');
        }
    }

    /**
     * Named constructor building the ID from the type and qualifier.
     *
     * @param array<string, mixed> $metadata
     * @param list<string> $tags
     */
    public static function make(
        NodeType $type,
        string $qualifier,
        string $label,
        ?string $group = null,
        ?FileReference $file = null,
        array $metadata = [],
        array $tags = [],
    ): self {
        return new self(
            id: $type->id($qualifier),
            type: $type,
            label: $label,
            group: $group,
            file: $file,
            metadata: $metadata,
            tags: $tags,
        );
    }

    /**
     * @param array<string,mixed> $data
     *
     * @throws InvalidArgumentException
     * @throws ValueError
     * @throws TypeError
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;
        $type = $data['type'] ?? null;
        $label = $data['label'] ?? null;

        if (!is_string($id) || !is_string($type) || !is_string($label)) {
            throw new InvalidArgumentException('Node array requires string keys: id, type, label.');
        }

        $file = $data['file'] ?? null;
        $metadata = $data['metadata'] ?? [];
        $tags = $data['tags'] ?? [];
        $group = $data['group'] ?? null;

        return new self(
            id: $id,
            type: NodeType::from($type),
            label: $label,
            group: is_string($group) ? $group : null,
            file: is_array($file) ? FileReference::fromArray($file) : null,
            metadata: is_array($metadata) ? $metadata : [],
            tags: is_array($tags) ? array_values(array_filter($tags, is_string(...))) : [],
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): NodeType
    {
        return $this->type;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function group(): ?string
    {
        return $this->group;
    }

    public function file(): ?FileReference
    {
        return $this->file;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'label' => $this->label,
            'group' => $this->group,
            'file' => $this->file?->toArray(),
            'metadata' => $this->metadata,
            'tags' => $this->tags,
        ];
    }
}
