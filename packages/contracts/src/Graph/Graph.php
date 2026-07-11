<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Graph;

/**
 * Default graph implementation with ID-based duplicate prevention.
 */
final class Graph implements GraphInterface
{
    /** @var array<string, NodeInterface> */
    private array $nodes = [];

    /** @var array<string, EdgeInterface> */
    private array $edges = [];

    public function nodes(): array
    {
        return array_values($this->nodes);
    }

    public function edges(): array
    {
        return array_values($this->edges);
    }

    public function addNode(NodeInterface $node): void
    {
        if (!isset($this->nodes[$node->id()])) {
            $this->nodes[$node->id()] = $node;
        }
    }

    public function addEdge(EdgeInterface $edge): void
    {
        if (!isset($this->edges[$edge->id()])) {
            $this->edges[$edge->id()] = $edge;
        }
    }

    public function hasNode(string $id): bool
    {
        return isset($this->nodes[$id]);
    }

    public function hasEdge(string $id): bool
    {
        return isset($this->edges[$id]);
    }

    public function findNode(string $id): ?NodeInterface
    {
        return $this->nodes[$id] ?? null;
    }

    public function merge(GraphInterface $other): void
    {
        foreach ($other->nodes() as $node) {
            $this->addNode($node);
        }

        foreach ($other->edges() as $edge) {
            $this->addEdge($edge);
        }
    }

    public function nodeCount(): int
    {
        return count($this->nodes);
    }

    public function edgeCount(): int
    {
        return count($this->edges);
    }

    public function toArray(): array
    {
        return [
            'nodes' => array_map(
                static fn (NodeInterface $node): array => $node->toArray(),
                array_values($this->nodes),
            ),
            'edges' => array_map(
                static fn (EdgeInterface $edge): array => $edge->toArray(),
                array_values($this->edges),
            ),
        ];
    }
}
