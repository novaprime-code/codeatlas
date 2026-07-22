<?php

declare(strict_types=1);

namespace CodeAtlas\Analyzers\Routes\DTOs;

/**
 * The accumulated attributes of the route group(s) currently in scope.
 *
 * As the extractor descends into nested Route::group() closures, each level
 * merges its attributes into the parent context: prefixes concatenate,
 * middleware lists merge, name prefixes concatenate, and domain overrides.
 *
 * Immutable — descending creates a new context via merge(), so sibling
 * branches never see each other's mutations.
 */
final readonly class GroupContext
{
    /**
     * @param list<string> $middleware
     */
    public function __construct(
        public string $prefix = '',
        public array $middleware = [],
        public string $namePrefix = '',
        public ?string $domain = null,
    ) {}

    public static function root(): self
    {
        return new self();
    }

    /**
     * @param list<string> $middleware
     */
    public function merge(
        string $prefix = '',
        array $middleware = [],
        string $namePrefix = '',
        ?string $domain = null,
    ): self {
        return new self(
            prefix: $this->joinPrefix($this->prefix, $prefix),
            middleware: [...$this->middleware, ...$middleware],
            namePrefix: $this->namePrefix . $namePrefix,
            domain: $domain ?? $this->domain,
        );
    }

    /**
     * Combine the group prefix with a route URI, normalizing slashes.
     * "" + "/users" => "/users"; "api" + "users" => "/api/users".
     */
    public function applyUri(string $uri): string
    {
        $combined = $this->joinPrefix($this->prefix, $uri);

        return '/' . ltrim($combined, '/');
    }

    public function applyName(?string $name): ?string
    {
        if ($name === null) {
            return $this->namePrefix === '' ? null : $this->namePrefix;
        }

        return $this->namePrefix . $name;
    }

    private function joinPrefix(string $left, string $right): string
    {
        $l = trim($left, '/');
        $r = trim($right, '/');

        if ($l === '') {
            return $r;
        }

        if ($r === '') {
            return $l;
        }

        return $l . '/' . $r;
    }
}
