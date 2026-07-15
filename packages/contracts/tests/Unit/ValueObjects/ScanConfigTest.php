<?php

declare(strict_types=1);

use CodeAtlas\Contracts\ValueObjects\ScanConfig;

describe('ScanConfig', function (): void {
    it('provides sensible Laravel defaults', function (): void {
        $c = ScanConfig::default();
        expect($c->paths)->toContain('app', 'routes', 'config');
        expect($c->excludePatterns)->toContain('vendor', 'node_modules');
        expect($c->fileExtensions)->toBe(['.php']);
    });

    it('withPaths() returns a new instance with overridden paths', function (): void {
        $orig = ScanConfig::default();
        $mod = $orig->withPaths('src', 'lib');

        expect($mod->paths)->toBe(['src', 'lib']);
        expect($orig->paths)->toContain('app');
        expect($mod->excludePatterns)->toBe($orig->excludePatterns);
    });

    it('withExclusions() returns a new instance with overridden exclusions', function (): void {
        $mod = ScanConfig::default()->withExclusions('build', 'dist');
        expect($mod->excludePatterns)->toBe(['build', 'dist']);
    });
});
