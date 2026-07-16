<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\EdgeType;
use CodeAtlas\Contracts\Enums\NodeType;
use CodeAtlas\Contracts\Enums\Severity;
use CodeAtlas\Contracts\Graph\Edge;
use CodeAtlas\Contracts\Graph\Node;
use CodeAtlas\Contracts\ValueObjects\AnalysisError;
use CodeAtlas\Contracts\ValueObjects\AnalysisResult;

describe('AnalysisResult', function (): void {
    it('is empty by default apart from the analyzer name', function (): void {
        $r = new AnalysisResult(analyzer: 'routes');
        expect($r->analyzer)->toBe('routes');
        expect($r->nodes)->toBe([]);
        expect($r->edges)->toBe([]);
        expect($r->errors)->toBe([]);
    });

    it('merges nodes, edges, metadata, and errors into a new instance', function (): void {
        $a = new AnalysisResult(
            analyzer: 'routes',
            nodes: [Node::make(NodeType::Route, 'r1', 'R1')],
            metadata: ['duration_ms' => 10],
        );

        $b = new AnalysisResult(
            analyzer: 'routes',
            edges: [Edge::make('a', 'b', EdgeType::Calls)],
            metadata: ['files_scanned' => 5],
            errors: [new AnalysisError('routes', Severity::Warning, 'oops')],
        );

        $merged = $a->merge($b);

        expect($merged->nodes)->toHaveCount(1);
        expect($merged->edges)->toHaveCount(1);
        expect($merged->metadata)->toBe(['duration_ms' => 10, 'files_scanned' => 5]);
        expect($merged->errors)->toHaveCount(1);
        expect($a->edges)->toBe([]);
    });

    it('appends an error via withError() without mutating', function (): void {
        $r = new AnalysisResult(analyzer: 'routes');
        $with = $r->withError(new AnalysisError('routes', Severity::Error, 'boom'));

        expect($r->errors)->toBe([]);
        expect($with->errors)->toHaveCount(1);
    });

    it('serializes nodes, edges, and errors to arrays', function (): void {
        $r = new AnalysisResult(
            analyzer: 'routes',
            nodes: [Node::make(NodeType::Route, 'r1', 'R1')],
            edges: [Edge::make('a', 'b', EdgeType::Calls)],
            errors: [new AnalysisError('routes', Severity::Warning, 'skipped one')],
        );

        $arr = $r->toArray();

        expect($arr['analyzer'])->toBe('routes');
        expect($arr['nodes'])->toHaveCount(1);
        expect($arr['edges'])->toHaveCount(1);
        expect($arr['errors'][0])->toMatchArray(['severity' => 'warning', 'message' => 'skipped one']);
    });
});
