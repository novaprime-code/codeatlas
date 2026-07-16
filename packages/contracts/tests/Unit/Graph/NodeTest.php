<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\FileType;
use CodeAtlas\Contracts\Enums\NodeType;
use CodeAtlas\Contracts\Graph\Node;
use CodeAtlas\Contracts\ValueObjects\FileReference;

describe('Node', function (): void {
    it('constructs with required fields', function (): void {
        $node = new Node(
            id: 'route::get::/api/users',
            type: NodeType::Route,
            label: 'GET /api/users',
        );

        expect($node->id())->toBe('route::get::/api/users');
        expect($node->type())->toBe(NodeType::Route);
        expect($node->label())->toBe('GET /api/users');
        expect($node->group())->toBeNull();
        expect($node->file())->toBeNull();
        expect($node->metadata())->toBe([]);
        expect($node->tags())->toBe([]);
    });

    it('rejects an empty ID', function (): void {
        new Node(id: '', type: NodeType::Route, label: 'x');
    })->throws(InvalidArgumentException::class);

    it('builds a node via make() with derived ID', function (): void {
        $node = Node::make(NodeType::Controller, 'App\\UserController', 'UserController');
        expect($node->id())->toBe('controller::App\\UserController');
        expect($node->label())->toBe('UserController');
    });

    it('serializes to array with all fields', function (): void {
        $file = new FileReference('routes/api.php', '/app/routes/api.php', FileType::Route, 15, 15);
        $node = Node::make(
            NodeType::Route,
            'get::/api/users',
            'GET /api/users',
            group: 'api',
            file: $file,
            metadata: ['method' => 'GET'],
            tags: ['api', 'authenticated'],
        );

        $arr = $node->toArray();

        expect($arr)->toMatchArray([
            'id' => 'route::get::/api/users',
            'type' => 'route',
            'label' => 'GET /api/users',
            'group' => 'api',
            'metadata' => ['method' => 'GET'],
            'tags' => ['api', 'authenticated'],
        ]);
        expect($arr['file'])->toBeArray();
    });

    it('round-trips through fromArray()', function (): void {
        $original = Node::make(NodeType::Model, 'App\\User', 'User', metadata: ['table' => 'users']);
        $rebuilt = Node::fromArray($original->toArray());

        expect($rebuilt->id())->toBe($original->id());
        expect($rebuilt->type())->toBe($original->type());
        expect($rebuilt->metadata())->toBe($original->metadata());
    });

    it('rejects malformed arrays in fromArray()', function (): void {
        Node::fromArray(['id' => 'x']);
    })->throws(InvalidArgumentException::class);
});
