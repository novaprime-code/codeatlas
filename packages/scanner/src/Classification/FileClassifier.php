<?php

declare(strict_types=1);

namespace CodeAtlas\Scanner\Classification;

use CodeAtlas\Contracts\Enums\FileType;

/**
 * Classify a project-relative path into a FileType by convention.
 *
 * The classifier is intentionally pure: input is a normalized relative
 * path (forward slashes, no leading slash), output is a FileType. It
 * never touches the filesystem — that's the walker's job.
 *
 * Classification is layered:
 *   1. Directory prefix match (most specific first)
 *   2. Filename-based fallback (routes, artisan, composer.json)
 *   3. FileType::Other for anything unrecognized
 *
 * The prefix table is `list<array{string, FileType}>` so ordering is
 * deterministic — more specific paths shadow less specific ones (e.g.
 * `app/Http/Controllers/` is checked before `app/`).
 */
final class FileClassifier
{
    /**
     * Directory prefix → FileType, evaluated in order.
     *
     * @var list<array{0: string, 1: FileType}>
     */
    private const PREFIX_MAP = [
        ['app/Http/Controllers/', FileType::Controller],
        ['app/Http/Middleware/',  FileType::Middleware],
        ['app/Services/',         FileType::Service],
        ['app/Repositories/',     FileType::Repository],
        ['app/Models/',           FileType::Model],
        ['app/Events/',           FileType::Event],
        ['app/Listeners/',        FileType::Listener],
        ['app/Jobs/',             FileType::Job],
        ['app/Notifications/',    FileType::Notification],
        ['app/Policies/',         FileType::Policy],
        ['app/Console/',          FileType::Command],
        ['app/Providers/',        FileType::Provider],
        ['app/Observers/',        FileType::Other],
        ['database/migrations/',  FileType::Migration],
        ['database/factories/',   FileType::Factory],
        ['database/seeders/',     FileType::Seeder],
        ['resources/views/',      FileType::View],
        ['config/',               FileType::Config],
        ['routes/',               FileType::Route],
    ];

    /**
     * @param array<string, FileType> $customOverrides Project-relative prefix => FileType
     */
    public function __construct(private readonly array $customOverrides = []) {}

    public function classify(string $relativePath): FileType
    {
        $normalized = $this->normalize($relativePath);

        foreach ($this->customOverrides as $prefix => $type) {
            if (str_starts_with($normalized, $this->normalize($prefix))) {
                return $type;
            }
        }

        foreach (self::PREFIX_MAP as [$prefix, $type]) {
            if (str_starts_with($normalized, $prefix)) {
                return $type;
            }
        }

        return FileType::Other;
    }

    private function normalize(string $path): string
    {
        $unified = str_replace('\\', '/', $path);

        return ltrim($unified, '/');
    }
}
