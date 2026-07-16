<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Config;

use CodeAtlas\Contracts\ConfigInterface;
use CodeAtlas\Contracts\Exceptions\ConfigurationException;

/**
 * Configuration container with dot-notation access and deep merging.
 *
 * Values are stored as a nested associative array. Dot keys ('scanner.paths')
 * traverse the tree. Missing keys return the caller's default; use has() to
 * disambiguate "absent" from "explicitly null".
 */
final class Config implements ConfigInterface
{
    /**
     * @param array<string, mixed> $items
     */
    public function __construct(private array $items = []) {}

    /**
     * @param array<string, mixed> $items
     */
    public static function fromArray(array $items): self
    {
        return new self($items);
    }

    /**
     * Load configuration from a PHP file that returns an array.
     *
     * @throws ConfigurationException
     */
    public static function fromFile(string $path): self
    {
        if (!is_file($path) || !is_readable($path)) {
            throw ConfigurationException::fileNotReadable($path);
        }

        /** @var mixed $loaded */
        $loaded = require $path;

        if (!is_array($loaded)) {
            throw ConfigurationException::invalidValue($path, 'file must return an array');
        }

        /** @var array<string, mixed> $loaded */
        return new self($loaded);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($key === '') {
            return $default;
        }

        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        $segments = explode('.', $key);
        /** @var array<string, mixed> */
        $cursor = $this->items;

        foreach ($segments as $segment) {
            if (!is_array($cursor) || !array_key_exists($segment, $cursor)) {
                return $default;
            }
            /** @var mixed $cursor */
            $cursor = $cursor[$segment];
        }

        return $cursor;
    }

    public function has(string $key): bool
    {
        if ($key === '') {
            return false;
        }

        if (array_key_exists($key, $this->items)) {
            return true;
        }

        $segments = explode('.', $key);
        /** @var array<string, mixed> */
        $cursor = $this->items;

        foreach ($segments as $segment) {
            if (!is_array($cursor) || !array_key_exists($segment, $cursor)) {
                return false;
            }
            /** @var mixed $cursor */
            $cursor = $cursor[$segment];
        }

        return true;
    }

    public function set(string $key, mixed $value): void
    {
        if ($key === '') {
            return;
        }

        $segments = explode('.', $key);
        $cursor = &$this->items;

        foreach ($segments as $i => $segment) {
            if ($i === count($segments) - 1) {
                $cursor[$segment] = $value;

                return;
            }

            if (!isset($cursor[$segment]) || !is_array($cursor[$segment])) {
                $cursor[$segment] = [];
            }

            /** @var array<string, mixed> $next */
            $next = &$cursor[$segment];
            $cursor = &$next;
        }
    }

    /**
     * Deep-merge another Config into a new instance (right wins on scalar collision).
     */
    public function merge(self $other): self
    {
        return new self($this->deepMerge($this->items, $other->items));
    }

    public function all(): array
    {
        return $this->items;
    }

    /**
     * @param array<string, mixed> $base
     * @param array<string, mixed> $overlay
     *
     * @return array<string, mixed>
     */
    private function deepMerge(array $base, array $overlay): array
    {
        foreach ($overlay as $key => $value) {
            if (is_array($value) && isset($base[$key]) && is_array($base[$key]) && $this->isAssoc($value) && $this->isAssoc($base[$key])) {
                /** @var array<string, mixed> $baseChild */
                $baseChild = $base[$key];
                /** @var array<string, mixed> $value */
                $base[$key] = $this->deepMerge($baseChild, $value);
            } else {
                $base[$key] = $value;
            }
        }

        return $base;
    }

    /**
     * @param array<mixed> $array
     */
    private function isAssoc(array $array): bool
    {
        if ($array === []) {
            return true;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }
}
