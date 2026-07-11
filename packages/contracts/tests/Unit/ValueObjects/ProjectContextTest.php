<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\FileType;
use CodeAtlas\Contracts\ValueObjects\FileReference;
use CodeAtlas\Contracts\ValueObjects\ProjectContext;

function makeFile(string $path, FileType $type): FileReference
{
    return new FileReference($path, '/proj/' . $path, $type);
}

describe('ProjectContext', function (): void {
    it('exposes project metadata', function (): void {
        $ctx = new ProjectContext(
            name: 'demo',
            path: '/proj',
            framework: 'laravel',
            frameworkVersion: '11.x',
            phpVersion: '8.3.12',
            files: [],
        );

        expect($ctx->name)->toBe('demo');
        expect($ctx->framework)->toBe('laravel');
        expect($ctx->frameworkVersion)->toBe('11.x');
    });

    it('filters files by type', function (): void {
        $ctx = new ProjectContext(
            name: 'demo', path: '/proj', framework: 'laravel',
            frameworkVersion: null, phpVersion: null,
            files: [
                makeFile('app/Models/User.php', FileType::Model),
                makeFile('app/Models/Post.php', FileType::Model),
                makeFile('routes/web.php', FileType::Route),
            ],
        );

        expect($ctx->filesOfType(FileType::Model))->toHaveCount(2);
        expect($ctx->filesOfType(FileType::Route))->toHaveCount(1);
        expect($ctx->filesOfType(FileType::Job))->toHaveCount(0);
    });

    it('reports file counts', function (): void {
        $ctx = new ProjectContext(
            name: 'demo', path: '/proj', framework: 'laravel',
            frameworkVersion: null, phpVersion: null,
            files: [
                makeFile('app/Models/User.php', FileType::Model),
                makeFile('routes/web.php', FileType::Route),
                makeFile('routes/api.php', FileType::Route),
            ],
        );

        expect($ctx->fileCount())->toBe(3);
        expect($ctx->fileCounts())->toBe(['model' => 1, 'route' => 2]);
    });

    it('serializes to array', function (): void {
        $ctx = new ProjectContext(
            name: 'demo', path: '/proj', framework: 'laravel',
            frameworkVersion: '11.x', phpVersion: '8.3.12', files: [],
            autoloadPsr4: ['App\\' => 'app/'],
        );

        $arr = $ctx->toArray();

        expect($arr)->toMatchArray([
            'name' => 'demo',
            'framework' => 'laravel',
            'framework_version' => '11.x',
            'php_version' => '8.3.12',
        ]);
        expect($arr['autoload_psr4'])->toBe(['App\\' => 'app/']);
    });
});
