<?php

declare(strict_types=1);

use CodeAtlas\Core\Events\EventBus;
use CodeAtlas\Core\Events\Events;

describe('EventBus', function (): void {
    it('starts with no listeners', function (): void {
        expect((new EventBus())->hasListeners('x'))->toBeFalse();
    });

    it('registers a listener and dispatches to it', function (): void {
        $bus = new EventBus();
        $got = null;
        $bus->listen('x', function (mixed $payload) use (&$got): void { $got = $payload; });
        $bus->dispatch('x', 'hello');
        expect($got)->toBe('hello');
    });

    it('fires listeners in registration order', function (): void {
        $bus = new EventBus();
        $order = [];
        $bus->listen('y', function () use (&$order): void { $order[] = 1; });
        $bus->listen('y', function () use (&$order): void { $order[] = 2; });
        $bus->dispatch('y');
        expect($order)->toBe([1, 2]);
    });

    it('is a no-op for events with no listeners', function (): void {
        (new EventBus())->dispatch('never-registered', 'ignored');
        expect(true)->toBeTrue();
    });

    it('forgets listeners for an event', function (): void {
        $bus = new EventBus();
        $bus->listen('x', fn () => null);
        $bus->forget('x');
        expect($bus->hasListeners('x'))->toBeFalse();
    });
});

describe('Events canonical names', function (): void {
    it('exposes pipeline lifecycle constants', function (): void {
        expect(Events::PIPELINE_STARTED)->toBe('pipeline.started');
        expect(Events::PIPELINE_COMPLETED)->toBe('pipeline.completed');
        expect(Events::SCAN_STARTED)->toBe('scan.started');
        expect(Events::ANALYSIS_ERROR)->toBe('analysis.error');
    });
});
