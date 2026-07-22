<?php

declare(strict_types=1);

use CodeAtlas\Analyzers\Routes\RouteAnalyzer;
use CodeAtlas\Contracts\Enums\EdgeType;
use CodeAtlas\Contracts\Enums\FileType;
use CodeAtlas\Contracts\Enums\NodeType;
use CodeAtlas\Core\Parser\PhpParser;
use CodeAtlas\Scanner\Scanner;

function appPath(): string
{
    return __DIR__ . '/../Fixtures/integration-app';
}

describe('RouteAnalyzer — full pipeline', function (): void {
    it('discovers route files and produces route nodes', function (): void {
        $context = Scanner::default()->scan(appPath());
        expect($context->filesOfType(FileType::Route))->toHaveCount(2);

        $result = (new RouteAnalyzer(new PhpParser()))->analyze($context);

        expect($result->analyzer)->toBe('routes');
        expect($result->nodes)->toHaveCount(4);
        expect($result->errors)->toBe([]);
        expect($result->metadata['files_analyzed'])->toBe(2);
    });

    it('generates Route→Controller and Route→Middleware edges', function (): void {
        $context = Scanner::default()->scan(appPath());
        $result = (new RouteAnalyzer(new PhpParser()))->analyze($context);

        $routesTo = array_filter($result->edges, fn ($e): bool => $e->type() === EdgeType::RoutesTo);
        $usesMw = array_filter($result->edges, fn ($e): bool => $e->type() === EdgeType::UsesMiddleware);

        expect($routesTo)->not->toBeEmpty();
        expect($usesMw)->not->toBeEmpty();
    });

    it('assigns deterministic, unique node IDs', function (): void {
        $context = Scanner::default()->scan(appPath());
        $result = (new RouteAnalyzer(new PhpParser()))->analyze($context);

        $ids = array_map(fn ($n): string => $n->id(), $result->nodes);
        expect($ids)->toContain('route::get::/users', 'route::get::/api/users');
        expect(count($ids))->toBe(count(array_unique($ids)));
    });

    it('tags nodes with route characteristics', function (): void {
        $context = Scanner::default()->scan(appPath());
        $result = (new RouteAnalyzer(new PhpParser()))->analyze($context);

        $byId = [];
        foreach ($result->nodes as $n) {
            $byId[$n->id()] = $n;
        }
        expect($byId['route::get::/users']->tags())->toContain('named', 'has-middleware');
    });

    it('supports only the Route node type', function (): void {
        expect((new RouteAnalyzer(new PhpParser()))->supportedNodeTypes())->toBe([NodeType::Route]);
    });
});
