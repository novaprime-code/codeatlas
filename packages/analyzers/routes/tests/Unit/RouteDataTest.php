<?php

declare(strict_types=1);

use CodeAtlas\Analyzers\Routes\DTOs\GroupContext;
use CodeAtlas\Analyzers\Routes\DTOs\RouteData;

describe('RouteData', function (): void {
    it('builds a node ID qualifier from verb and URI', function (): void {
        $route = new RouteData(uri: '/api/users', methods: ['GET']);
        expect($route->idQualifier())->toBe('get::/api/users');
    });

    it('joins multiple methods in the qualifier', function (): void {
        $route = new RouteData(uri: '/webhook', methods: ['GET', 'POST']);
        expect($route->idQualifier())->toBe('get|post::/webhook');
    });

    it('produces a human-readable label', function (): void {
        expect((new RouteData(uri: '/x', methods: ['GET']))->label())->toBe('GET /x');
    });

    it('serializes to the JSON schema shape', function (): void {
        $route = new RouteData(
            uri: '/users',
            methods: ['GET'],
            name: 'users.index',
            controller: 'App\\UserController',
            action: 'index',
        );
        $arr = $route->toArray();
        expect($arr)->toHaveKeys([
            'uri', 'methods', 'name', 'controller', 'action',
            'is_closure', 'middleware', 'prefix', 'domain', 'where', 'parameters',
        ]);
    });
});

describe('GroupContext', function (): void {
    it('applies an empty prefix as a leading slash', function (): void {
        expect(GroupContext::root()->applyUri('users'))->toBe('/users');
    });

    it('joins prefix and URI with normalized slashes', function (): void {
        $ctx = GroupContext::root()->merge(prefix: 'api');
        expect($ctx->applyUri('/users'))->toBe('/api/users');
    });

    it('accumulates nested prefixes', function (): void {
        $ctx = GroupContext::root()->merge(prefix: 'api')->merge(prefix: 'v1');
        expect($ctx->applyUri('users'))->toBe('/api/v1/users');
    });

    it('merges middleware across levels', function (): void {
        $ctx = GroupContext::root()->merge(middleware: ['a'])->merge(middleware: ['b', 'c']);
        expect($ctx->middleware)->toBe(['a', 'b', 'c']);
    });

    it('concatenates name prefixes', function (): void {
        $ctx = GroupContext::root()->merge(namePrefix: 'admin.')->merge(namePrefix: 'users.');
        expect($ctx->applyName('index'))->toBe('admin.users.index');
    });

    it('overrides the domain from the innermost group', function (): void {
        $ctx = GroupContext::root()->merge(domain: 'a.test')->merge(domain: 'b.test');
        expect($ctx->domain)->toBe('b.test');
    });

    it('does not mutate parent contexts', function (): void {
        $parent = GroupContext::root()->merge(prefix: 'api');
        $child = $parent->merge(prefix: 'v1');
        expect($parent->applyUri('x'))->toBe('/api/x');
        expect($child->applyUri('x'))->toBe('/api/v1/x');
    });
});
