<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\FileType;

describe('FileType', function (): void {
    it('has an Other fallback for unclassified files', function (): void {
        expect(FileType::Other->value)->toBe('other');
    });

    it('covers all standard Laravel directories', function (): void {
        $required = ['route', 'controller', 'middleware', 'service', 'repository',
            'model', 'event', 'listener', 'job', 'notification', 'policy',
            'command', 'migration', 'factory', 'seeder', 'provider', 'config', 'view'];

        $values = array_map(static fn(FileType $t): string => $t->value, FileType::cases());

        foreach ($required as $r) {
            expect($values)->toContain($r);
        }
    });
});
