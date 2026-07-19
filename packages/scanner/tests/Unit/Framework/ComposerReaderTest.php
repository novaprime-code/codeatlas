<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Exceptions\ScannerException;
use CodeAtlas\Scanner\Framework\ComposerReader;

function fixtures(): string
{
    return __DIR__ . '/../../Fixtures';
}

describe('ComposerReader — successful reads', function (): void {
    it('extracts the project name', function (): void {
        $meta = (new ComposerReader())->tryRead(fixtures() . '/laravel-app');
        expect($meta?->name)->toBe('demo/laravel-app');
    });

    it('extracts the PHP requirement', function (): void {
        $meta = (new ComposerReader())->tryRead(fixtures() . '/laravel-app');
        expect($meta?->phpRequirement)->toBe('^8.3');
    });

    it('reports required package versions', function (): void {
        $meta = (new ComposerReader())->tryRead(fixtures() . '/laravel-app');
        expect($meta?->requiresPackage('laravel/framework'))->toBeTrue();
        expect($meta?->versionOf('laravel/framework'))->toBe('^11.0');
    });

    it('extracts every PSR-4 autoload mapping (require + require-dev)', function (): void {
        $meta = (new ComposerReader())->tryRead(fixtures() . '/laravel-app');
        expect($meta?->autoloadPsr4)->toHaveKey('App\\', 'app/');
        expect($meta?->autoloadPsr4)->toHaveKey('Database\\Factories\\', 'database/factories/');
    });
});

describe('ComposerReader — absence and errors', function (): void {
    it('returns null when composer.json is missing', function (): void {
        expect((new ComposerReader())->tryRead(fixtures() . '/empty-project'))->toBeNull();
    });

    it('throws ScannerException on malformed JSON', function (): void {
        $tmp = sys_get_temp_dir() . '/bad-composer-' . uniqid();
        mkdir($tmp);
        file_put_contents($tmp . '/composer.json', '{not json');
        try {
            (new ComposerReader())->read($tmp);
        } finally {
            @unlink($tmp . '/composer.json');
            @rmdir($tmp);
        }
    })->throws(ScannerException::class, 'not valid JSON');
});
