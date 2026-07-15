<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Enums\Severity;

describe('Severity', function (): void {
    it('has the four standard levels', function (): void {
        $values = array_map(static fn (Severity $s): string => $s->value, Severity::cases());
        expect($values)->toEqualCanonicalizing(['error', 'warning', 'info', 'debug']);
    });
});
