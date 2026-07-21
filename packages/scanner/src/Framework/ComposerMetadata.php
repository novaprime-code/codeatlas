<?php

declare(strict_types=1);

namespace CodeAtlas\Scanner\Framework;

/**
 * Extracted composer.json metadata.
 *
 * "requires" and "requiresDev" preserve the raw dependency map so callers
 * can inspect any package version constraint. "autoloadPsr4" is the merged
 * autoload + autoload-dev PSR-4 mapping used by analyzers to resolve FQCNs.
 */
final readonly class ComposerMetadata
{
    /**
     * @param array<string, string> $requires
     * @param array<string, string> $requiresDev
     * @param array<string, string> $autoloadPsr4
     */
    public function __construct(
        public ?string $name,
        public ?string $phpRequirement,
        public array $requires = [],
        public array $requiresDev = [],
        public array $autoloadPsr4 = [],
    ) {}

    /**
     * @param array<string, mixed> $decoded
     */
    public static function fromArray(array $decoded): self
    {
        $name = self::stringOrNull($decoded['name'] ?? null);

        $require = self::stringMap($decoded['require'] ?? []);
        $requireDev = self::stringMap($decoded['require-dev'] ?? []);
        $phpRequirement = $require['php'] ?? null;

        $autoload = self::extractPsr4($decoded['autoload'] ?? []);
        $autoloadDev = self::extractPsr4($decoded['autoload-dev'] ?? []);

        return new self(
            name: $name,
            phpRequirement: $phpRequirement,
            requires: $require,
            requiresDev: $requireDev,
            autoloadPsr4: array_merge($autoload, $autoloadDev),
        );
    }

    public function requiresPackage(string $package): bool
    {
        return isset($this->requires[$package]) || isset($this->requiresDev[$package]);
    }

    public function versionOf(string $package): ?string
    {
        return $this->requires[$package] ?? $this->requiresDev[$package] ?? null;
    }

    private static function stringOrNull(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }

    /**
     * @return array<string, string>
     */
    private static function stringMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $out = [];

        foreach ($value as $k => $v) {
            if (is_string($k) && is_string($v)) {
                $out[$k] = $v;
            }
        }

        return $out;
    }

    /**
     * @return array<string, string>
     */
    private static function extractPsr4(mixed $autoload): array
    {
        if (!is_array($autoload)) {
            return [];
        }

        $psr4 = $autoload['psr-4'] ?? null;

        if (!is_array($psr4)) {
            return [];
        }

        $out = [];

        foreach ($psr4 as $prefix => $path) {
            if (!is_string($prefix)) {
                continue;
            }

            if (is_string($path)) {
                $out[$prefix] = $path;

                continue;
            }

            if (is_array($path)) {
                foreach ($path as $entry) {
                    if (is_string($entry)) {
                        $out[$prefix] = $entry;

                        break;
                    }
                }
            }
        }

        return $out;
    }
}
