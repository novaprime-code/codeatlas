<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\EdgeType;
use CodeAtlas\Contracts\Enums\NodeType;
use CodeAtlas\Contracts\Enums\Severity;
use CodeAtlas\Contracts\Graph\Edge;
use CodeAtlas\Contracts\Graph\Node;
use CodeAtlas\Contracts\ValueObjects\AnalysisError;
use CodeAtlas\Contracts\ValueObjects\AnalysisResult;
use CodeAtlas\Contracts\ValueObjects\ExportConfig;
use CodeAtlas\Exporters\Json\JsonExporter;

/**
 * @return array<string, mixed>
 */
function decode(string $json): array
{
    /** @var array<string, mixed> */
    return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
}

describe('JsonExporter — document structure', function (): void {
    it('is named "json"', function (): void {
        expect((new JsonExporter())->name())->toBe('json');
    });

    it('stamps the schema URL and version on every document', function (): void {
        $doc = decode((new JsonExporter())->export(new AnalysisResult(analyzer: 'routes'), ExportConfig::default())->content);
        expect($doc['$schema'])->toBe(JsonExporter::SCHEMA_URL);
        expect($doc['version'])->toBe(JsonExporter::SCHEMA_VERSION);
    });

    it('emits every top-level block even for an empty result', function (): void {
        $doc = decode((new JsonExporter())->export(new AnalysisResult(analyzer: 'routes'), ExportConfig::default())->content);
        expect($doc)->toHaveKeys(['$schema', 'version', 'project', 'analysis', 'graph', 'results', 'errors']);
        expect($doc['graph'])->toBe(['nodes' => [], 'edges' => []]);
    });

    it('sets JSON mime type and canonical filename', function (): void {
        $out = (new JsonExporter())->export(new AnalysisResult(analyzer: 'routes'), ExportConfig::default());
        expect($out->mimeType)->toBe('application/json');
        expect($out->filename)->toBe('codeatlas-analysis.json');
    });
});

describe('JsonExporter — project block', function (): void {
    it('populates project fields from ExportConfig options', function (): void {
        $config = new ExportConfig(options: [
            'project' => [
                'name' => 'demo/app',
                'path' => '/srv/app',
                'framework' => 'laravel',
                'framework_version' => '^11.0',
                'php_version' => '^8.3',
            ],
        ]);
        $doc = decode((new JsonExporter())->export(new AnalysisResult(analyzer: 'routes'), $config)->content);
        expect($doc['project']['name'])->toBe('demo/app');
        expect($doc['project']['framework_version'])->toBe('^11.0');
    });

    it('degrades missing project info to nulls, never omits keys', function (): void {
        $doc = decode((new JsonExporter())->export(new AnalysisResult(analyzer: 'routes'), ExportConfig::default())->content);
        expect($doc['project'])->toBe([
            'name' => null, 'path' => null, 'framework' => null,
            'framework_version' => null, 'php_version' => null,
        ]);
    });
});

describe('JsonExporter — analysis block', function (): void {
    it('writes an ISO-8601 timestamp', function (): void {
        $doc = decode((new JsonExporter())->export(new AnalysisResult(analyzer: 'routes'), ExportConfig::default())->content);
        expect($doc['analysis']['timestamp'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/');
    });

    it('takes duration_ms from options', function (): void {
        $config = new ExportConfig(options: ['duration_ms' => 1250]);
        $doc = decode((new JsonExporter())->export(new AnalysisResult(analyzer: 'routes'), $config)->content);
        expect($doc['analysis']['duration_ms'])->toBe(1250);
    });

    it('lists contributing analyzers', function (): void {
        $doc = decode((new JsonExporter())->export(new AnalysisResult(analyzer: 'routes'), ExportConfig::default())->content);
        expect($doc['analysis']['analyzers'])->toBe(['routes']);
    });
});

describe('JsonExporter — graph block', function (): void {
    it('serializes nodes and edges', function (): void {
        $result = new AnalysisResult(
            analyzer: 'routes',
            nodes: [Node::make(NodeType::Route, 'get::/x', 'GET /x')],
            edges: [Edge::make('route::get::/x', 'controller::App\\C', EdgeType::RoutesTo)],
        );
        $doc = decode((new JsonExporter())->export($result, ExportConfig::default())->content);
        expect($doc['graph']['nodes'])->toHaveCount(1);
        expect($doc['graph']['nodes'][0]['id'])->toBe('route::get::/x');
        expect($doc['graph']['edges'][0]['type'])->toBe('routes_to');
    });

    it('serializes empty maps as JSON objects, not arrays', function (): void {
        $result = new AnalysisResult(
            analyzer: 'routes',
            edges: [Edge::make('a', 'b', EdgeType::RoutesTo)],
        );
        $json = (new JsonExporter())->export($result, ExportConfig::default())->content;
        expect($json)->toContain('"metadata": {}');
        expect($json)->not->toContain('"metadata": []');
    });
});

describe('JsonExporter — results and errors blocks', function (): void {
    it('keys single-analyzer results under the analyzer name', function (): void {
        $result = new AnalysisResult(analyzer: 'routes', metadata: ['routes' => 7]);
        $doc = decode((new JsonExporter())->export($result, ExportConfig::default())->content);
        expect($doc['results']['routes']['routes'])->toBe(7);
    });

    it('passes through merged pipeline results as the analyzer map', function (): void {
        $result = new AnalysisResult(analyzer: 'pipeline', metadata: [
            'routes' => ['routes' => 7],
            'controllers' => ['controllers' => 3],
        ]);
        $doc = decode((new JsonExporter())->export($result, ExportConfig::default())->content);
        expect($doc['results'])->toHaveKeys(['routes', 'controllers']);
        expect($doc['analysis']['analyzers'])->toBe(['routes', 'controllers']);
    });

    it('exports analysis errors with string severities', function (): void {
        $result = new AnalysisResult(
            analyzer: 'routes',
            errors: [new AnalysisError('routes', Severity::Warning, 'bad file', file: 'routes/x.php')],
        );
        $doc = decode((new JsonExporter())->export($result, ExportConfig::default())->content);
        expect($doc['errors'])->toHaveCount(1);
        expect($doc['errors'][0]['severity'])->toBe('warning');
    });
});

describe('JsonExporter — pretty print', function (): void {
    it('honours ExportConfig::prettyPrint', function (): void {
        $result = new AnalysisResult(analyzer: 'routes');
        $pretty = (new JsonExporter())->export($result, new ExportConfig(prettyPrint: true));
        $compact = (new JsonExporter())->export($result, new ExportConfig(prettyPrint: false));

        expect($pretty->content)->toContain("\n");
        expect(strlen($compact->content))->toBeLessThan(strlen($pretty->content));
    });
});
