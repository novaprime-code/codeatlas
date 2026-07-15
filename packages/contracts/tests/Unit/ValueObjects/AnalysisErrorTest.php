<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\Severity;
use CodeAtlas\Contracts\ValueObjects\AnalysisError;

describe('AnalysisError', function (): void {
    it('exposes readonly properties', function (): void {
        $e = new AnalysisError('routes', Severity::Warning, 'skipped', 'routes/api.php', 42);

        expect($e->analyzer)->toBe('routes');
        expect($e->severity)->toBe(Severity::Warning);
        expect($e->message)->toBe('skipped');
        expect($e->file)->toBe('routes/api.php');
        expect($e->line)->toBe(42);
    });

    it('serializes to array with severity as its wire value', function (): void {
        $e = new AnalysisError('routes', Severity::Error, 'boom');
        expect($e->toArray()['severity'])->toBe('error');
    });
});
