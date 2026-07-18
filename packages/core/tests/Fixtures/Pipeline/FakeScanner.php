<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Pipeline;

use CodeAtlas\Contracts\Enums\FileType;
use CodeAtlas\Contracts\ScannerInterface;
use CodeAtlas\Contracts\ValueObjects\FileReference;
use CodeAtlas\Contracts\ValueObjects\ProjectContext;
use CodeAtlas\Contracts\ValueObjects\ScanConfig;

final class FakeScanner implements ScannerInterface
{
    public function scan(string $path, ?ScanConfig $config = null): ProjectContext
    {
        return new ProjectContext(
            name: 'demo',
            path: $path,
            framework: 'laravel',
            frameworkVersion: '11.x',
            phpVersion: '8.3.6',
            files: [
                new FileReference('routes/api.php', $path . '/routes/api.php', FileType::Route),
            ],
        );
    }
}
