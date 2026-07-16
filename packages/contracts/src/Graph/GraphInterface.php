<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Graph;

/**
 * A collection of nodes and edges forming the architecture graph.
 *
 * Duplicate node/edge IDs are silently ignored on add — the first
 * registration wins. This makes merging results from multiple analyzers
 * idempotent and order-independent for identical entities.
 */
interface GraphInterface
{
    /**
     * @return list<NodeInterface>
     */
    public function nodes(): array;

    /**
     * @return list<EdgeInterface>
     */
    public function edges(): array;

    public function addNode(NodeInterface $node): void;

    public function addEdge(EdgeInterface $edge): void;

    public function hasNode(string $id): bool;

    public function hasEdge(string $id): bool;

    public function findNode(string $id): ?NodeInterface;

    /**
     * Merge another graph into this one (duplicates ignored).
     */
    public function merge(GraphInterface $other): void;

    public function nodeCount(): int;

    public function edgeCount(): int;

    /**
     * @return array{nodes: list<array<string, mixed>>, edges: list<array<string, mixed>>}
     */
    public function toArray(): array;
}
