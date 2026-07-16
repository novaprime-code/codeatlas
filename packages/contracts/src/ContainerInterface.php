<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts;

use CodeAtlas\Contracts\Exceptions\ContainerException;

/**
 * Minimal dependency injection container contract.
 *
 * Intentionally small: bind, resolve, tag. The core implementation adds
 * reflection-based auto-resolution; consumers should only rely on this API.
 */
interface ContainerInterface
{
    /**
     * @param class-string $abstract
     * @param class-string|callable(self): object $concrete
     */
    public function bind(string $abstract, string|callable $concrete): void;

    /**
     * @param class-string $abstract
     * @param class-string|callable(self): object $concrete
     */
    public function singleton(string $abstract, string|callable $concrete): void;

    /**
     * @template T of object
     *
     * @param class-string<T> $abstract
     *
     * @return T
     *
     * @throws ContainerException When the abstract cannot be resolved
     */
    public function make(string $abstract): object;

    /**
     * @param class-string $abstract
     */
    public function has(string $abstract): bool;

    /**
     * @param class-string $abstract
     */
    public function tag(string $abstract, string $tag): void;

    /**
     * Resolve every binding registered under a tag.
     *
     * @return list<object>
     *
     * @throws ContainerException
     */
    public function tagged(string $tag): array;
}
