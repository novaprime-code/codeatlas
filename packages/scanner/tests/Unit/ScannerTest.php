<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\FileType;
use CodeAtlas\Contracts\Exceptions\ScannerException;
use CodeAtlas\Contracts\ValueObjects\ScanConfig;
use CodeAtlas\Scanner\Scanner;

function fx(string $suffix = ''): string
{
    return __DIR__ . '/../Fixtures' . ($suffix === '' ? '' : '/' . $suffix);
}

describe('Scanner — full Laravel fixture', function (): void {
    it('returns a ProjectContext with correct metadata', function (): void {
        $ctx = Scanner::default()->scan(fx('laravel-app'));

        expect($ctx->name)->toBe('demo/laravel-app');
        expect($ctx->framework)->toBe('laravel');
        expect($ctx->frameworkVersion)->toBe('^11.0');
        expect($ctx->phpVersion)->toBe('^8.3');
        expect($ctx->autoloadPsr4)->toHaveKey('App\\');
    });

    it('discovers files across every standard Laravel directory', function (): void {
        $ctx = Scanner::default()->scan(fx('laravel-app'));
        $counts = $ctx->fileCounts();

        expect($counts['controller'] ?? 0)->toBeGreaterThanOrEqual(1);
        expect($counts['model'] ?? 0)->toBe(2);
        expect($counts['route'] ?? 0)->toBe(2);
        expect($counts['config'] ?? 0)->toBe(2);
        expect($counts['migration'] ?? 0)->toBe(1);
        expect($counts['view'] ?? 0)->toBe(1);
    });

    it('excludes vendor/ and tests/ from results', function (): void {
        $ctx = Scanner::default()->scan(fx('laravel-app'));
        $paths = array_map(fn ($f) => $f->path, $ctx->files);

        expect(array_filter($paths, fn ($p) => str_starts_with($p, 'vendor/')))->toBe([]);
        expect(array_filter($paths, fn ($p) => str_starts_with($p, 'tests/')))->toBe([]);
    });

    it('filesOfType returns the right subset', function (): void {
        $ctx = Scanner::default()->scan(fx('laravel-app'));

        expect($ctx->filesOfType(FileType::Controller))->toHaveCount(1);
        expect($ctx->filesOfType(FileType::Model))->toHaveCount(2);
    });
});

describe('Scanner — empty and non-Laravel projects', function (): void {
    it('returns a zero-file context for an empty directory', function (): void {
        $ctx = Scanner::default()->scan(fx('empty-project'));
        expect($ctx->fileCount())->toBe(0);
        expect($ctx->framework)->toBe('unknown');
    });

    it('identifies non-Laravel PHP projects as unknown', function (): void {
        $ctx = Scanner::default()->scan(fx('non-laravel'));
        expect($ctx->framework)->toBe('unknown');
        expect($ctx->name)->toBe('demo/plain-lib');
    });
});

describe('Scanner — error paths', function (): void {
    it('throws when the path does not exist', function (): void {
        Scanner::default()->scan('/absolutely/does/not/exist');
    })->throws(ScannerException::class);

    it('throws when the path is a file, not a directory', function (): void {
        Scanner::default()->scan(__FILE__);
    })->throws(ScannerException::class);
});

describe('Scanner — custom ScanConfig', function (): void {
    it('honours a restricted path list', function (): void {
        $only = new ScanConfig(paths: ['app/Models'], excludePatterns: [], fileExtensions: ['.php']);
        $ctx = Scanner::default()->scan(fx('laravel-app'), $only);
        expect($ctx->fileCount())->toBe(2);
        expect($ctx->filesOfType(FileType::Model))->toHaveCount(2);
    });
});
