<?php

declare(strict_types=1);

namespace CodeAtlas\Scanner\Framework;

/**
 * Identify the framework backing a project directory.
 *
 * Currently detects Laravel; Symfony/Rails/etc. will be added when their
 * analyzers land. Detection is intentionally conservative — a project is
 * only marked Laravel if it has an `artisan` file AND declares
 * laravel/framework in composer.json. That rules out generic Symfony
 * projects that happen to have an `app/` directory.
 */
final class FrameworkDetector
{
    public const FRAMEWORK_LARAVEL = 'laravel';
    public const FRAMEWORK_UNKNOWN = 'unknown';

    public function detect(string $projectPath, ?ComposerMetadata $composer): FrameworkResult
    {
        $artisan = rtrim($projectPath, '/\\') . '/artisan';
        $hasArtisan = is_file($artisan);

        if ($composer?->requiresPackage('laravel/framework') === true && $hasArtisan) {
            return new FrameworkResult(
                framework: self::FRAMEWORK_LARAVEL,
                version: $composer->versionOf('laravel/framework'),
            );
        }

        return new FrameworkResult(
            framework: self::FRAMEWORK_UNKNOWN,
            version: null,
        );
    }
}
