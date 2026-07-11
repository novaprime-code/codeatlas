<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\ValueObjects;

use CodeAtlas\Contracts\Graph\EdgeInterface;
use CodeAtlas\Contracts\Graph\NodeInterface;

/**
 * The output of a single analyzer run.
 *
 * Contains graph nodes, edges, per-analyzer metadata, and any non-fatal
 * errors that occurred during analysis. The pipeline merges results from
 * every analyzer into a single Graph.
 */
final readonly class AnalysisResult
{
    /**
     * @param list<NodeInterface> $nodes
     * @param list<EdgeInterface> $edges
     * @param array<string, mixed> $metadata
     * @param list<AnalysisError> $errors
     */
    public function __construct(
        public string $analyzer,
        public array $nodes = [],
        public array $edges = [],
        public array $metadata = [],
        public array $errors = [],
    ) {}

    /**
     * Merge another result into a new one. The analyzer name of the receiver
     * is preserved; metadata is shallow-merged (right wins on key collision).
     */
    public function merge(self $other): self
    {
        return new self(
            analyzer: $this->analyzer,
            nodes: [...$this->nodes, ...$other->nodes],
            edges: [...$this->edges, ...$other->edges],
            metadata: [...$this->metadata, ...$other->metadata],
            errors: [...$this->errors, ...$other->errors],
        );
    }

    public function withError(AnalysisError $error): self
    {
        return new self(
            analyzer: $this->analyzer,
            nodes: $this->nodes,
            edges: $this->edges,
            metadata: $this->metadata,
            errors: [...$this->errors, $error],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'analyzer' => $this->analyzer,
            'nodes' => array_map(
                static fn (NodeInterface $node): array => $node->toArray(),
                $this->nodes,
            ),
            'edges' => array_map(
                static fn (EdgeInterface $edge): array => $edge->toArray(),
                $this->edges,
            ),
            'metadata' => $this->metadata,
            'errors' => array_map(
                static fn (AnalysisError $error): array => $error->toArray(),
                $this->errors,
            ),
        ];
    }
}
