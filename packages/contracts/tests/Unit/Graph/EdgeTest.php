<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\EdgeType;
use CodeAtlas\Contracts\Graph\Edge;

describe('Edge', function (): void {
    it('constructs and exposes its properties', function (): void {
        $edge = new Edge(
            id: 'edge::1',
            source: 'route::get::/users',
            target: 'controller::App\\UserController',
            type: EdgeType::RoutesTo,
            label: 'handles',
        );

        expect($edge->source())->toBe('route::get::/users');
        expect($edge->target())->toBe('controller::App\\UserController');
        expect($edge->type())->toBe(EdgeType::RoutesTo);
        expect($edge->label())->toBe('handles');
    });

    it('make() builds a deterministic ID from endpoints and type', function (): void {
        $edge = Edge::make('a', 'b', EdgeType::Calls);
        expect($edge->id())->toBe('edge::calls::a->b');
    });

    it('produces identical IDs for identical inputs', function (): void {
        $e1 = Edge::make('x', 'y', EdgeType::DependsOn);
        $e2 = Edge::make('x', 'y', EdgeType::DependsOn);
        expect($e1->id())->toBe($e2->id());
    });

    it('rejects empty IDs, sources, or targets', function (): void {
        new Edge(id: '', source: 'a', target: 'b', type: EdgeType::Calls);
    })->throws(InvalidArgumentException::class);

    it('serializes and deserializes symmetrically', function (): void {
        $original = Edge::make('a', 'b', EdgeType::Extends, label: 'inherits');
        $rebuilt = Edge::fromArray($original->toArray());

        expect($rebuilt->id())->toBe($original->id());
        expect($rebuilt->type())->toBe($original->type());
        expect($rebuilt->label())->toBe($original->label());
    });
});
