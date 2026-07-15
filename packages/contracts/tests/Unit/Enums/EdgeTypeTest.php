<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\EdgeType;

describe('EdgeType', function (): void {
    it('covers every relationship kind in the schema', function (): void {
        $expected = [
            'routes_to', 'calls', 'depends_on', 'extends', 'implements',
            'uses_trait', 'uses_middleware', 'has_relationship',
            'dispatches', 'listens_to', 'queues', 'notifies',
            'authorizes', 'schedules', 'migrates',
        ];

        $actual = array_map(static fn (EdgeType $t): string => $t->value, EdgeType::cases());

        expect($actual)->toEqualCanonicalizing($expected);
    });
});
