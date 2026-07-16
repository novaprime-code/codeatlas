<?php

declare(strict_types=1);

use CodeAtlas\Contracts\ValueObjects\ExportConfig;
use CodeAtlas\Contracts\ValueObjects\ExportOutput;

describe('ExportConfig', function (): void {
    it('has sensible defaults', function (): void {
        $c = ExportConfig::default();
        expect($c->prettyPrint)->toBeTrue();
        expect($c->outputPath)->toBeNull();
    });
});

describe('ExportOutput', function (): void {
    it('reports its byte count', function (): void {
        $o = new ExportOutput('hello world', 'application/json', 'analysis.json');
        expect($o->byteCount())->toBe(11);
    });
});
