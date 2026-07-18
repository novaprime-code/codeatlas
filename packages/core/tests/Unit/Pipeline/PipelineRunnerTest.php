<?php

declare(strict_types=1);

use CodeAtlas\Core\Container\Container;
use CodeAtlas\Core\Events\EventBus;
use CodeAtlas\Core\Events\Events;
use CodeAtlas\Core\Pipeline\PipelineResult;
use CodeAtlas\Core\Pipeline\PipelineRunner;
use CodeAtlas\Core\Plugin\PluginLoader;
use CodeAtlas\Core\Tests\Fixtures\Pipeline\FailingAnalyzer;
use CodeAtlas\Core\Tests\Fixtures\Pipeline\FakeJsonExporter;
use CodeAtlas\Core\Tests\Fixtures\Pipeline\FakeScanner;
use CodeAtlas\Core\Tests\Fixtures\Pipeline\SecondAnalyzer;
use CodeAtlas\Core\Tests\Fixtures\Pipeline\WorkingAnalyzer;

function pipelineHarness(): array
{
    $container = new Container();
    $loader = new PluginLoader($container);

    $container->singleton(WorkingAnalyzer::class, WorkingAnalyzer::class);
    $container->singleton(FailingAnalyzer::class, FailingAnalyzer::class);
    $container->singleton(SecondAnalyzer::class, SecondAnalyzer::class);
    $container->singleton(FakeJsonExporter::class, FakeJsonExporter::class);

    $loader->tagAnalyzer(WorkingAnalyzer::class);
    $loader->tagAnalyzer(FailingAnalyzer::class);
    $loader->tagAnalyzer(SecondAnalyzer::class);

    $events = new EventBus();
    $runner = new PipelineRunner($container, new FakeScanner(), $events);

    return [$runner, $events];
}

describe('PipelineRunner — end-to-end run', function (): void {
    it('returns a PipelineResult carrying context, results, graph, exports, and duration', function (): void {
        [$runner] = pipelineHarness();
        $result = $runner->run('/fake', exporters: [FakeJsonExporter::class]);

        expect($result)->toBeInstanceOf(PipelineResult::class);
        expect($result->context->framework)->toBe('laravel');
        expect($result->results)->toHaveCount(3);
        expect($result->graph->nodeCount())->toBe(2);
        expect($result->graph->edgeCount())->toBe(1);
        expect($result->exports)->toHaveKey('json');
        expect($result->durationMs)->toBeGreaterThanOrEqual(0);
    });

    it('records failing analyzer errors without crashing the run', function (): void {
        [$runner] = pipelineHarness();
        $result = $runner->run('/fake');

        expect($result->errorCount())->toBe(1);
        expect($result->analyzerNames())->toContain('failing');
    });
});

describe('PipelineRunner — filtering', function (): void {
    it('runs only the analyzers named in the filter', function (): void {
        [$runner] = pipelineHarness();
        $result = $runner->run('/fake', analyzerFilter: ['second']);

        expect($result->results)->toHaveCount(1);
        expect($result->results[0]->analyzer)->toBe('second');
    });

    it('runs all analyzers when the filter is null', function (): void {
        [$runner] = pipelineHarness();
        expect($runner->run('/fake', analyzerFilter: null)->results)->toHaveCount(3);
    });
});

describe('PipelineRunner — events', function (): void {
    it('dispatches lifecycle events in order', function (): void {
        [$runner, $events] = pipelineHarness();

        $fired = [];
        $events->listen(Events::PIPELINE_STARTED, function () use (&$fired): void {
            $fired[] = 'pipeline.started';
        });
        $events->listen(Events::SCAN_COMPLETED, function () use (&$fired): void {
            $fired[] = 'scan.completed';
        });
        $events->listen(Events::PIPELINE_COMPLETED, function () use (&$fired): void {
            $fired[] = 'pipeline.completed';
        });

        $runner->run('/fake');

        expect($fired)->toBe(['pipeline.started', 'scan.completed', 'pipeline.completed']);
    });

    it('dispatches analysis.error for failing analyzers', function (): void {
        [$runner, $events] = pipelineHarness();

        $errored = [];
        $events->listen(Events::ANALYSIS_ERROR, function (string $name) use (&$errored): void {
            $errored[] = $name;
        });

        $runner->run('/fake');

        expect($errored)->toBe(['failing']);
    });
});

describe('PipelineRunner — graph merging', function (): void {
    it('deduplicates nodes with identical IDs across analyzers', function (): void {
        [$runner] = pipelineHarness();
        $result = $runner->run('/fake');

        $ids = array_map(fn($n) => $n->id(), $result->graph->nodes());
        expect(count($ids))->toBe(count(array_unique($ids)));
    });
});
