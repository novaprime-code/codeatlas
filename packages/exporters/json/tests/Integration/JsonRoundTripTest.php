<?php

declare(strict_types=1);

use CodeAtlas\Analyzers\Routes\RouteAnalyzer;
use CodeAtlas\Contracts\ValueObjects\ExportConfig;
use CodeAtlas\Core\Parser\PhpParser;
use CodeAtlas\Exporters\Json\JsonExporter;
use CodeAtlas\Scanner\Scanner;

describe('JSON round-trip through the full pipeline', function (): void {
    it('produces a schema-conformant document from a real Laravel fixture', function (): void {
        $appPath = dirname(__DIR__, 4) . '/analyzers/routes/tests/Fixtures/integration-app';

        $context = Scanner::default()->scan($appPath);
        $result = (new RouteAnalyzer(new PhpParser()))->analyze($context);

        $config = new ExportConfig(prettyPrint: true, options: [
            'project' => [
                'name' => $context->name,
                'path' => $context->path,
                'framework' => $context->framework,
                'framework_version' => $context->frameworkVersion,
                'php_version' => $context->phpVersion,
            ],
        ]);

        $out = (new JsonExporter())->export($result, $config);

        /** @var array<string, mixed> $doc */
        $doc = json_decode($out->content, true, 512, JSON_THROW_ON_ERROR);

        expect($doc['project']['name'])->toBe('demo/integration-app');
        expect($doc['project']['framework'])->toBe('laravel');
        expect($doc['graph']['nodes'])->toHaveCount(4);
        expect($doc['results']['routes']['files_analyzed'])->toBe(2);
        expect($doc['errors'])->toBe([]);

        $ids = array_column($doc['graph']['nodes'], 'id');
        expect($ids)->toContain('route::get::/users', 'route::get::/api/users');
    });
});
