<?php

declare(strict_types=1);

use CodeAtlas\Scanner\Framework\ComposerReader;
use CodeAtlas\Scanner\Framework\FrameworkDetector;

describe('FrameworkDetector', function (): void {
    it('identifies a Laravel project with artisan + composer dependency', function (): void {
        $reader = new ComposerReader();
        $meta = $reader->tryRead(__DIR__ . '/../../Fixtures/laravel-app');
        $result = (new FrameworkDetector())->detect(__DIR__ . '/../../Fixtures/laravel-app', $meta);
        expect($result->framework)->toBe('laravel');
        expect($result->version)->toBe('^11.0');
        expect($result->isKnown())->toBeTrue();
    });

    it('returns unknown for non-Laravel projects', function (): void {
        $meta = (new ComposerReader())->tryRead(__DIR__ . '/../../Fixtures/non-laravel');
        $result = (new FrameworkDetector())->detect(__DIR__ . '/../../Fixtures/non-laravel', $meta);
        expect($result->framework)->toBe('unknown');
        expect($result->isKnown())->toBeFalse();
    });

    it('requires both artisan AND composer dependency (not just artisan)', function (): void {
        $tmp = sys_get_temp_dir() . '/fake-laravel-' . uniqid();
        mkdir($tmp);
        touch($tmp . '/artisan');
        try {
            expect((new FrameworkDetector())->detect($tmp, null)->framework)->toBe('unknown');
        } finally {
            @unlink($tmp . '/artisan');
            @rmdir($tmp);
        }
    });
});
