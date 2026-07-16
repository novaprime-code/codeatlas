<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Container;

use CodeAtlas\Contracts\ContainerInterface;
use CodeAtlas\Contracts\Exceptions\ContainerException;
use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Minimal PSR-flavoured DI container.
 *
 * No Laravel, no Symfony. Reflection-based auto-resolution walks
 * type-hinted constructor parameters. Bindings can be tagged so the
 * pipeline can enumerate all analyzers, exporters, or plugins as a group.
 *
 * Concurrent make() calls into the same abstract are detected as circular
 * dependencies via a resolution stack.
 */
final class Container implements ContainerInterface
{
    /** @var array<class-string, Binding> */
    private array $bindings = [];

    /** @var array<class-string, object> */
    private array $instances = [];

    /** @var array<string, list<class-string>> */
    private array $tags = [];

    /** @var array<class-string, true> */
    private array $resolving = [];

    public function bind(string $abstract, string|callable $concrete): void
    {
        $this->bindings[$abstract] = new Binding($concrete, shared: false);
        unset($this->instances[$abstract]);
    }

    public function singleton(string $abstract, string|callable $concrete): void
    {
        $this->bindings[$abstract] = new Binding($concrete, shared: true);
        unset($this->instances[$abstract]);
    }

    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
        $this->bindings[$abstract] = new Binding($instance::class, shared: true);
    }

    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]) || class_exists($abstract);
    }

    public function make(string $abstract): object
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (isset($this->resolving[$abstract])) {
            throw ContainerException::circularDependency($abstract);
        }

        $this->resolving[$abstract] = true;

        try {
            $binding = $this->bindings[$abstract] ?? null;
            $object = $binding === null
                ? $this->build($abstract)
                : $this->resolveBinding($binding, $abstract);

            if ($binding !== null && $binding->shared) {
                $this->instances[$abstract] = $object;
            }

            return $object;
        } finally {
            unset($this->resolving[$abstract]);
        }
    }

    public function tag(string $abstract, string $tag): void
    {
        $this->tags[$tag] ??= [];

        if (!in_array($abstract, $this->tags[$tag], true)) {
            $this->tags[$tag][] = $abstract;
        }
    }

    public function tagged(string $tag): array
    {
        $abstracts = $this->tags[$tag] ?? [];

        return array_map(fn (string $abstract): object => $this->make($abstract), $abstracts);
    }

    /**
     * @param class-string $abstract
     */
    private function resolveBinding(Binding $binding, string $abstract): object
    {
        $concrete = $binding->concrete;

        if ($concrete instanceof Closure || (is_callable($concrete) && !is_string($concrete))) {
            /** @var callable(self): object $concrete */
            $result = $concrete($this);

            if (!is_object($result)) {
                throw ContainerException::unresolvableParameter($abstract, 'callable-return');
            }

            return $result;
        }

        /** @var class-string $concrete */
        return $this->build($concrete);
    }

    /**
     * @param class-string $class
     */
    private function build(string $class): object
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw ContainerException::notBound($class);
        }

        if (!$reflection->isInstantiable()) {
            throw ContainerException::notBound($class);
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return $reflection->newInstance();
        }

        $args = array_map(
            fn (ReflectionParameter $param): mixed => $this->resolveParameter($param, $class),
            $constructor->getParameters(),
        );

        return $reflection->newInstanceArgs($args);
    }

    /**
     * @param class-string $forClass
     */
    private function resolveParameter(ReflectionParameter $param, string $forClass): mixed
    {
        $type = $param->getType();

        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            /** @var class-string $className */
            $className = $type->getName();

            return $this->make($className);
        }

        if ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }

        if ($param->allowsNull()) {
            return null;
        }

        throw ContainerException::unresolvableParameter($forClass, $param->getName());
    }
}
