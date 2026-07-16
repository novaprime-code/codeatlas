<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Exceptions\ParserException;
use CodeAtlas\Core\Parser\PhpParser;

describe('PhpParser — disk parsing', function (): void {
    it('parses a file from disk', function (): void {
        $tmp = tempnam(sys_get_temp_dir(), 'parser_');
        file_put_contents($tmp, "<?php\n\nnamespace App;\n\nclass Foo {}\n");
        try {
            $parsed = (new PhpParser())->parse($tmp);
            expect($parsed->namespace())->toBe('App');
            expect($parsed->path())->toBe($tmp);
        } finally {
            @unlink($tmp);
        }
    });

    it('throws when the file does not exist', function (): void {
        (new PhpParser())->parse('/does/not/exist.php');
    })->throws(ParserException::class, 'not readable');

    it('throws with the offending path on syntax errors', function (): void {
        (new PhpParser())->parseString('<?php class Broken { public function oops() { return }', 'broken.php');
    })->throws(ParserException::class, 'broken.php');
});

describe('PhpParser — cache', function (): void {
    it('starts empty', function (): void {
        expect((new PhpParser())->cacheSize())->toBe(0);
    });

    it('caches identical source', function (): void {
        $p = new PhpParser();
        $source = "<?php\nclass A {}";
        $a = $p->parseString($source, 'a.php');
        $b = $p->parseString($source, 'a.php');
        expect($p->cacheSize())->toBe(1);
        expect($a)->toBe($b);
    });

    it('clears on request', function (): void {
        $p = new PhpParser();
        $p->parseString('<?php class A {}', 'a.php');
        $p->clearCache();
        expect($p->cacheSize())->toBe(0);
    });
});
