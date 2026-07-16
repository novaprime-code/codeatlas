<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Exceptions\ContainerException;
use CodeAtlas\Core\Container\Container;
use CodeAtlas\Core\Tests\Fixtures\Container\CircularA;
use CodeAtlas\Core\Tests\Fixtures\Container\InMemoryRepository;
use CodeAtlas\Core\Tests\Fixtures\Container\OptionalDep;
use CodeAtlas\Core\Tests\Fixtures\Container\RepositoryInterface;
use CodeAtlas\Core\Tests\Fixtures\Container\SimpleService;
use CodeAtlas\Core\Tests\Fixtures\Container\UnresolvableService;
use CodeAtlas\Core\Tests\Fixtures\Container\UserService;

describe('Container — basic bindings', function (): void {
    it('resolves a concrete class with no dependencies', function (): void {
        $c = new Container();
        expect($c->make(SimpleService::class))->toBeInstanceOf(SimpleService::class);
    });

    it('binds an interface to a concrete implementation', function (): void {
        $c = new Container();
        $c->bind(RepositoryInterface::class, InMemoryRepository::class);

        $resolved = $c->make(RepositoryInterface::class);
        expect($resolved)->toBeInstanceOf(InMemoryRepository::class);
        expect($resolved->find(1))->toBe('record-1');
    });

    it('resolves via a Closure factory', function (): void {
        $c = new Container();
        $c->bind(RepositoryInterface::class, fn (): RepositoryInterface => new InMemoryRepository());

        expect($c->make(RepositoryInterface::class))->toBeInstanceOf(InMemoryRepository::class);
    });
});

describe('Container — singletons', function (): void {
    it('returns the same instance for a singleton', function (): void {
        $c = new Container();
        $c->singleton(SimpleService::class, SimpleService::class);

        $a = $c->make(SimpleService::class);
        $b = $c->make(SimpleService::class);
        expect($a)->toBe($b);
    });

    it('returns fresh instances for non-singleton bindings', function (): void {
        $c = new Container();
        $c->bind(SimpleService::class, SimpleService::class);

        expect($c->make(SimpleService::class))->not->toBe($c->make(SimpleService::class));
    });

    it('accepts a pre-built instance via instance()', function (): void {
        $c = new Container();
        $preset = new SimpleService();
        $c->instance(SimpleService::class, $preset);

        expect($c->make(SimpleService::class))->toBe($preset);
    });
});

describe('Container — auto-resolution', function (): void {
    it('resolves constructor dependencies via reflection', function (): void {
        $c = new Container();
        $c->bind(RepositoryInterface::class, InMemoryRepository::class);

        $user = $c->make(UserService::class);
        expect($user)->toBeInstanceOf(UserService::class);
        expect($user->repository)->toBeInstanceOf(InMemoryRepository::class);
        expect($user->simple)->toBeInstanceOf(SimpleService::class);
    });

    it('uses default values for scalar parameters', function (): void {
        $c = new Container();
        $dep = $c->make(OptionalDep::class);
        expect($dep->count)->toBe(42);
        expect($dep->simple)->toBeInstanceOf(SimpleService::class);
    });

    it('throws when a required scalar has no default', function (): void {
        $c = new Container();
        $c->make(UnresolvableService::class);
    })->throws(ContainerException::class);
});

describe('Container — has()', function (): void {
    it('reports true for classes even without an explicit binding', function (): void {
        $c = new Container();
        expect($c->has(SimpleService::class))->toBeTrue();
    });

    it('reports true for explicit bindings', function (): void {
        $c = new Container();
        $c->bind(RepositoryInterface::class, InMemoryRepository::class);
        expect($c->has(RepositoryInterface::class))->toBeTrue();
    });
});

describe('Container — circular dependencies', function (): void {
    it('detects a two-node cycle', function (): void {
        $c = new Container();
        $c->make(CircularA::class);
    })->throws(ContainerException::class, 'Circular dependency');
});

describe('Container — tagged bindings', function (): void {
    it('collects tagged bindings via tagged()', function (): void {
        $c = new Container();
        $c->bind(RepositoryInterface::class, InMemoryRepository::class);
        $c->bind(SimpleService::class, SimpleService::class);
        $c->tag(RepositoryInterface::class, 'services');
        $c->tag(SimpleService::class, 'services');

        $tagged = $c->tagged('services');
        expect($tagged)->toHaveCount(2);
        expect($tagged[0])->toBeInstanceOf(InMemoryRepository::class);
        expect($tagged[1])->toBeInstanceOf(SimpleService::class);
    });

    it('is idempotent when tagging the same abstract twice', function (): void {
        $c = new Container();
        $c->bind(SimpleService::class, SimpleService::class);
        $c->tag(SimpleService::class, 'x');
        $c->tag(SimpleService::class, 'x');
        expect($c->tagged('x'))->toHaveCount(1);
    });

    it('returns an empty list for unknown tags', function (): void {
        expect((new Container())->tagged('nothing'))->toBe([]);
    });
});
