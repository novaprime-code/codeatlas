<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\EdgeType;
use CodeAtlas\Contracts\Enums\NodeType;
use CodeAtlas\Contracts\Graph\Edge;
use CodeAtlas\Contracts\Graph\Graph;
use CodeAtlas\Contracts\Graph\Node;

describe('Graph', function (): void {
    it('starts empty', function (): void {
        $g = new Graph();
        expect($g->nodeCount())->toBe(0);
        expect($g->edgeCount())->toBe(0);
        expect($g->nodes())->toBe([]);
        expect($g->edges())->toBe([]);
    });

    it('adds nodes and edges', function (): void {
        $g = new Graph();
        $g->addNode(Node::make(NodeType::Route, 'r1', 'R1'));
        $g->addEdge(Edge::make('a', 'b', EdgeType::Calls));

        expect($g->nodeCount())->toBe(1);
        expect($g->edgeCount())->toBe(1);
    });

    it('silently ignores duplicate node IDs (first-write-wins)', function (): void {
        $g = new Graph();
        $g->addNode(Node::make(NodeType::Route, 'r1', 'First'));
        $g->addNode(Node::make(NodeType::Route, 'r1', 'Second'));

        expect($g->nodeCount())->toBe(1);
        expect($g->findNode('route::r1')?->label())->toBe('First');
    });

    it('silently ignores duplicate edge IDs', function (): void {
        $g = new Graph();
        $g->addEdge(Edge::make('a', 'b', EdgeType::Calls));
        $g->addEdge(Edge::make('a', 'b', EdgeType::Calls));

        expect($g->edgeCount())->toBe(1);
    });

    it('reports node/edge existence via hasNode/hasEdge', function (): void {
        $g = new Graph();
        $g->addNode(Node::make(NodeType::Model, 'User', 'User'));
        expect($g->hasNode('model::User'))->toBeTrue();
        expect($g->hasNode('model::Ghost'))->toBeFalse();
    });

    it('merges another graph idempotently', function (): void {
        $a = new Graph();
        $a->addNode(Node::make(NodeType::Route, 'r1', 'R1'));

        $b = new Graph();
        $b->addNode(Node::make(NodeType::Route, 'r1', 'R1-again'));
        $b->addNode(Node::make(NodeType::Route, 'r2', 'R2'));

        $a->merge($b);

        expect($a->nodeCount())->toBe(2);
        expect($a->findNode('route::r1')?->label())->toBe('R1');
    });

    it('exports to a JSON-schema-shaped array', function (): void {
        $g = new Graph();
        $g->addNode(Node::make(NodeType::Route, 'r1', 'R1'));
        $g->addEdge(Edge::make('route::r1', 'controller::X', EdgeType::RoutesTo));

        $arr = $g->toArray();

        expect($arr)->toHaveKeys(['nodes', 'edges']);
        expect($arr['nodes'])->toHaveCount(1);
        expect($arr['edges'])->toHaveCount(1);
    });
});
