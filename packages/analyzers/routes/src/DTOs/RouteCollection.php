<?php

declare(strict_types=1);

namespace CodeAtlas\Analyzers\Routes\DTOs;

/**
 * An ordered collection of extracted routes from one or more files.
 */
final readonly class RouteCollection
{
    /**
     * @param list<RouteData> $routes
     */
    public function __construct(public array $routes = []) {}

    public function withRoute(RouteData $route): self
    {
        return new self([...$this->routes, $route]);
    }

    /**
     * @param list<RouteData> $routes
     */
    public function withRoutes(array $routes): self
    {
        return new self([...$this->routes, ...$routes]);
    }

    public function count(): int
    {
        return count($this->routes);
    }

    public function isEmpty(): bool
    {
        return $this->routes === [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function toArray(): array
    {
        return array_map(
            static fn(RouteData $route): array => $route->toArray(),
            $this->routes,
        );
    }
}
