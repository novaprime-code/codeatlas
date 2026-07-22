<?php

declare(strict_types=1);

use CodeAtlas\Analyzers\Routes\DTOs\RouteData;
use CodeAtlas\Analyzers\Routes\Extraction\RouteExtractor;
use CodeAtlas\Core\Parser\PhpParser;

/**
 * @return array<string, RouteData>
 */
function extractFrom(string $fixture): array
{
    $path = __DIR__ . '/../Fixtures/routes/' . $fixture . '.php.txt';
    $parser = new PhpParser();
    $parsed = $parser->parseString((string) file_get_contents($path), $path);
    $extractor = new RouteExtractor($parsed);

    $out = [];
    foreach ($extractor->extract($parsed->ast()) as $route) {
        $out[$route->label()] = $route;
    }

    return $out;
}

describe('RouteExtractor — basic verbs', function (): void {
    $routes = extractFrom('web');

    it('extracts every route in the file', function () use ($routes): void {
        expect($routes)->toHaveCount(6);
    });

    it('captures closure routes', function () use ($routes): void {
        expect($routes['GET /']->isClosure)->toBeTrue();
        expect($routes['GET /']->controller)->toBeNull();
    });

    it('resolves controller FQCN and action', function () use ($routes): void {
        expect($routes['GET /users']->controller)->toBe('App\\Http\\Controllers\\UserController');
        expect($routes['GET /users']->action)->toBe('index');
    });

    it('captures the route name', function () use ($routes): void {
        expect($routes['GET /users']->name)->toBe('users.index');
    });

    it('captures single and array middleware', function () use ($routes): void {
        expect($routes['POST /users']->middleware)->toBe(['auth']);
        expect($routes['GET /dashboard']->middleware)->toBe(['auth', 'verified']);
    });

    it('resolves invokable controllers to __invoke', function () use ($routes): void {
        expect($routes['GET /dashboard']->controller)->toBe('App\\Http\\Controllers\\DashboardController');
        expect($routes['GET /dashboard']->action)->toBe('__invoke');
    });

    it('captures where constraints and whereNumber shortcut', function () use ($routes): void {
        expect($routes['GET /users/{id}']->where)->toBe(['id' => '[0-9]+']);
        expect($routes['PUT /posts/{post}']->where)->toBe(['post' => '[0-9]+']);
    });

    it('extracts URI parameters', function () use ($routes): void {
        expect($routes['GET /users/{id}']->parameters)->toBe(['id']);
    });
});

describe('RouteExtractor — groups', function (): void {
    $routes = extractFrom('api');

    it('applies group prefix to URIs', function () use ($routes): void {
        expect($routes)->toHaveKey('GET /api/users');
    });

    it('inherits group middleware', function () use ($routes): void {
        expect($routes['GET /api/users']->middleware)->toBe(['api']);
    });

    it('accumulates middleware through nested groups', function () use ($routes): void {
        expect($routes['POST /api/users']->middleware)->toBe(['api', 'auth:sanctum']);
        expect($routes['DELETE /api/users/{id}']->middleware)->toBe(['api', 'auth:sanctum']);
    });

    it('concatenates group name prefixes', function () use ($routes): void {
        expect($routes['GET /api/users']->name)->toBe('api.users.index');
    });

    it('handles array-style group config', function () use ($routes): void {
        expect($routes['GET /v2/status']->name)->toBe('v2.status');
        expect($routes['GET /v2/status']->middleware)->toBe(['throttle:60,1']);
    });

    it('handles match() with multiple verbs', function () use ($routes): void {
        expect($routes['GET|POST /webhook']->methods)->toBe(['GET', 'POST']);
    });
});

describe('RouteExtractor — resource routes', function (): void {
    $routes = extractFrom('resource');

    it('expands resource() into 7 routes', function () use ($routes): void {
        $photos = array_filter($routes, fn(RouteData $r): bool => str_contains($r->uri, 'photos'));
        expect($photos)->toHaveCount(7);
    });

    it('expands apiResource() into 5 routes (no create/edit)', function () use ($routes): void {
        $comments = array_filter($routes, fn(RouteData $r): bool => str_contains($r->uri, 'comments'));
        expect($comments)->toHaveCount(5);
        expect($routes)->not->toHaveKey('GET /comments/{comment}/edit');
    });

    it('resolves the resource controller', function () use ($routes): void {
        expect($routes['GET /photos']->controller)->toBe('App\\Http\\Controllers\\PhotoController');
        expect($routes['GET /photos']->action)->toBe('index');
    });

    it('singularizes the route model parameter', function () use ($routes): void {
        expect($routes)->toHaveKey('GET /photos/{photo}');
    });
});

describe('RouteExtractor — empty file', function (): void {
    it('returns no routes for a file with only comments', function (): void {
        expect(extractFrom('empty'))->toBe([]);
    });
});
