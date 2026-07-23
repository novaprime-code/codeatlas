<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Container;

use Closure;
use CodeAtlas\Contracts\ContainerInterface;
use CodeAtlas\Contracts\Exceptions\ContainerException;
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
    /** @var array<string, Binding> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $instances = [];

    /** @var array<string, list<class-string>> */
    private array $tags = [];

    /** @var array<string, true> */
    private array $resolving = [];

    public function bind(string $abstract, string|callable $concrete): void
    {
        $this->bindings[$abstract] = new Binding($this->normalizeConcrete($concrete), shared: false);
        unset($this->instances[$abstract]);
    }

    public function singleton(string $abstract, string|callable $concrete): void
    {
        $this->bindings[$abstract] = new Binding($this->normalizeConcrete($concrete), shared: true);
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

    /**
     * @template T of object
     *
     * @param class-string<T> $abstract
     *
     * @return T
     */
    public function make(string $abstract): object
    {
        if (isset($this->instances[$abstract])) {
            /** @var T */
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

            /** @var T */
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
        $resolved = [];

        foreach ($this->tags[$tag] ?? [] as $abstract) {
            $resolved[] = $this->make($abstract);
        }

        return $resolved;
    }

    private function normalizeConcrete(string|callable $concrete): string|Closure
    {
        return is_string($concrete) ? $concrete : Closure::fromCallable($concrete);
    }

    /**
     * @param class-string $abstract
     */
    private function resolveBinding(Binding $binding, string $abstract): object
    {
        $concrete = $binding->concrete;

        if ($concrete instanceof Closure) {
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
        if (!class_exists($class)) {
            throw ContainerException::notBound($class);
        }

        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw ContainerException::notBound($class);
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return $reflection->newInstance();
        }

        $args = array_map(
            fn(ReflectionParameter $param): mixed => $this->resolveParameter($param, $class),
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

        if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
            /** @var class-string $className */
            $className = $type->getName();

            try {
                return $this->make($className);
            } catch (ContainerException $e) {
                if ($param->isDefaultValueAvailable()) {
                    try {
                        return $param->getDefaultValue();
                    } catch (ReflectionException) {
                        throw $e;
                    }
                }

                if ($param->allowsNull()) {
                    return null;
                }

                throw $e;
            }
        }

        if ($param->isDefaultValueAvailable()) {
            try {
                return $param->getDefaultValue();
            } catch (ReflectionException) {
                throw ContainerException::unresolvableParameter($forClass, $param->getName());
            }
        }

        if ($param->allowsNull()) {
            return null;
        }

        throw ContainerException::unresolvableParameter($forClass, $param->getName());
    }
}
