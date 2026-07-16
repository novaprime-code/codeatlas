<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\FileType;
use CodeAtlas\Contracts\ValueObjects\FileReference;

describe('FileReference', function (): void {
    it('exposes its properties as readonly', function (): void {
        $ref = new FileReference('app/Models/User.php', '/proj/app/Models/User.php', FileType::Model, 10, 95);

        expect($ref->path)->toBe('app/Models/User.php');
        expect($ref->absolutePath)->toBe('/proj/app/Models/User.php');
        expect($ref->type)->toBe(FileType::Model);
        expect($ref->lineStart)->toBe(10);
        expect($ref->lineEnd)->toBe(95);
    });

    it('rejects empty paths', function (): void {
        new FileReference('', '', FileType::Other);
    })->throws(InvalidArgumentException::class);

    it('rejects invalid line ranges', function (): void {
        new FileReference('a.php', '/a.php', FileType::Other, 10, 5);
    })->throws(InvalidArgumentException::class);

    it('rejects lineStart < 1', function (): void {
        new FileReference('a.php', '/a.php', FileType::Other, 0);
    })->throws(InvalidArgumentException::class);

    it('creates a new instance with a line range via withLineRange()', function (): void {
        $a = new FileReference('a.php', '/a.php', FileType::Other);
        $b = $a->withLineRange(5, 20);

        expect($a->lineStart)->toBe(1);
        expect($b->lineStart)->toBe(5);
        expect($b->lineEnd)->toBe(20);
    });

    it('round-trips through toArray/fromArray', function (): void {
        $original = new FileReference('a.php', '/a.php', FileType::Model, 3, 8);
        $rebuilt = FileReference::fromArray($original->toArray());

        expect($rebuilt->path)->toBe($original->path);
        expect($rebuilt->type)->toBe($original->type);
        expect($rebuilt->lineStart)->toBe(3);
        expect($rebuilt->lineEnd)->toBe(8);
    });
});
