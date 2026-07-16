<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Exceptions\ConfigurationException;
use CodeAtlas\Core\Config\Config;

describe('Config — construction', function (): void {
    it('starts empty when constructed with no items', function (): void {
        $c = new Config();
        expect($c->all())->toBe([]);
    });

    it('accepts an array via fromArray()', function (): void {
        $c = Config::fromArray(['scanner' => ['paths' => ['app']]]);
        expect($c->all())->toBe(['scanner' => ['paths' => ['app']]]);
    });

    it('loads from a PHP file via fromFile()', function (): void {
        $c = Config::fromFile(__DIR__ . '/../../Fixtures/Config/scanner.php');
        expect($c->get('scanner.paths'))->toBe(['app', 'routes', 'config']);
    });

    it('throws on missing file', function (): void {
        Config::fromFile('/does/not/exist.php');
    })->throws(ConfigurationException::class, 'not readable');

    it('throws when file does not return an array', function (): void {
        Config::fromFile(__DIR__ . '/../../Fixtures/Config/invalid.php');
    })->throws(ConfigurationException::class);
});

describe('Config — dot-notation get()', function (): void {
    $config = Config::fromArray([
        'scanner' => [
            'paths' => ['app', 'routes'],
            'nested' => ['deep' => ['value' => 42]],
        ],
        'flag' => true,
    ]);

    it('returns top-level values', function () use ($config): void {
        expect($config->get('flag'))->toBeTrue();
    });

    it('returns nested values via dot-notation', function () use ($config): void {
        expect($config->get('scanner.paths'))->toBe(['app', 'routes']);
        expect($config->get('scanner.nested.deep.value'))->toBe(42);
    });

    it('returns default for missing keys', function () use ($config): void {
        expect($config->get('missing', 'fallback'))->toBe('fallback');
        expect($config->get('scanner.does.not.exist', 99))->toBe(99);
    });

    it('returns null default when omitted', function () use ($config): void {
        expect($config->get('missing'))->toBeNull();
    });

    it('handles empty key gracefully', function () use ($config): void {
        expect($config->get(''))->toBeNull();
        expect($config->get('', 'default'))->toBe('default');
    });
});

describe('Config — has()', function (): void {
    $config = Config::fromArray(['a' => ['b' => ['c' => 1, 'd' => null]]]);

    it('reports true for existing nested keys', function () use ($config): void {
        expect($config->has('a.b.c'))->toBeTrue();
    });

    it('reports true for keys explicitly set to null', function () use ($config): void {
        expect($config->has('a.b.d'))->toBeTrue();
    });

    it('reports false for missing keys', function () use ($config): void {
        expect($config->has('a.b.z'))->toBeFalse();
        expect($config->has('nope'))->toBeFalse();
    });

    it('reports false for empty key', function () use ($config): void {
        expect($config->has(''))->toBeFalse();
    });
});

describe('Config — set()', function (): void {
    it('sets a top-level value', function (): void {
        $c = new Config();
        $c->set('flag', true);
        expect($c->get('flag'))->toBeTrue();
    });

    it('creates nested structure on demand', function (): void {
        $c = new Config();
        $c->set('scanner.paths.custom', ['x', 'y']);
        expect($c->get('scanner.paths.custom'))->toBe(['x', 'y']);
    });

    it('overwrites existing scalar with new nested tree', function (): void {
        $c = Config::fromArray(['x' => 'scalar']);
        $c->set('x.y', 1);
        expect($c->get('x.y'))->toBe(1);
    });
});

describe('Config — merge()', function (): void {
    it('deep-merges nested associative arrays', function (): void {
        $a = Config::fromArray(['scanner' => ['paths' => ['app'], 'timeout' => 30]]);
        $b = Config::fromArray(['scanner' => ['timeout' => 60, 'retries' => 3]]);
        $merged = $a->merge($b);

        expect($merged->get('scanner.paths'))->toBe(['app']);
        expect($merged->get('scanner.timeout'))->toBe(60);
        expect($merged->get('scanner.retries'))->toBe(3);
    });

    it('overwrites indexed arrays entirely (right wins)', function (): void {
        $a = Config::fromArray(['paths' => ['app', 'routes']]);
        $b = Config::fromArray(['paths' => ['src']]);
        $merged = $a->merge($b);

        expect($merged->get('paths'))->toBe(['src']);
    });

    it('returns a new instance without mutating either side', function (): void {
        $a = Config::fromArray(['x' => 1]);
        $b = Config::fromArray(['y' => 2]);
        $merged = $a->merge($b);

        expect($merged)->not->toBe($a);
        expect($a->has('y'))->toBeFalse();
        expect($b->has('x'))->toBeFalse();
        expect($merged->has('x'))->toBeTrue();
        expect($merged->has('y'))->toBeTrue();
    });
});
