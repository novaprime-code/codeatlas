<?php

declare(strict_types=1);

namespace CodeAtlas\Analyzers\Routes\DTOs;

/**
 * A single extracted route.
 *
 * This is the analyzer's internal representation, mapped 1:1 to the
 * "routes" result schema in JSON_SCHEMA.md. It is deliberately flat and
 * serializable — the analyzer converts these into graph Nodes/Edges, and
 * the JSON exporter serializes them into the per-analyzer result block.
 *
 * @phpstan-type WhereMap array<string, string>
 */
final readonly class RouteData
{
    /**
     * @param list<string> $methods HTTP verbs, uppercase (GET, POST, ...)
     * @param list<string> $middleware Fully accumulated middleware (group + route)
     * @param list<string> $parameters URI parameter names ({id} => "id")
     * @param WhereMap $where Parameter constraint patterns
     */
    public function __construct(
        public string $uri,
        public array $methods,
        public ?string $name = null,
        public ?string $controller = null,
        public ?string $action = null,
        public bool $isClosure = false,
        public array $middleware = [],
        public ?string $prefix = null,
        public ?string $domain = null,
        public array $where = [],
        public array $parameters = [],
        public ?int $line = null,
    ) {}

    /**
     * Deterministic node ID qualifier: "get::/api/users" or "get|post::/x".
     */
    public function idQualifier(): string
    {
        $verb = strtolower(implode('|', $this->methods));

        return $verb . '::' . $this->uri;
    }

    /**
     * Human-readable label: "GET /api/users".
     */
    public function label(): string
    {
        return implode('|', $this->methods) . ' ' . $this->uri;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'uri' => $this->uri,
            'methods' => $this->methods,
            'name' => $this->name,
            'controller' => $this->controller,
            'action' => $this->action,
            'is_closure' => $this->isClosure,
            'middleware' => $this->middleware,
            'prefix' => $this->prefix,
            'domain' => $this->domain,
            'where' => $this->where,
            'parameters' => $this->parameters,
            'line' => $this->line,
        ];
    }
}
