<?php

declare(strict_types=1);

use CodeAtlas\Core\Parser\PhpParser;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;

function loadFixture(string $name): string
{
    return (string) file_get_contents(__DIR__ . '/../../Fixtures/Parser/' . $name . '.php.txt');
}

describe('ParsedFile — namespace and classes', function (): void {
    it('extracts the namespace of a namespaced file', function (): void {
        $parsed = (new PhpParser())->parseString(loadFixture('SimpleClass'));
        expect($parsed->namespace())->toBe('App\\Http\\Controllers');
    });

    it('reports null for a file without a namespace', function (): void {
        $parsed = (new PhpParser())->parseString(loadFixture('NoNamespace'));
        expect($parsed->namespace())->toBeNull();
    });

    it('extracts fully qualified names of every class-like declaration', function (): void {
        $parsed = (new PhpParser())->parseString(loadFixture('MultipleClasses'));
        $names = $parsed->classNames();
        expect($names)->toEqualCanonicalizing([
            'App\\Domain\\Contract',
            'App\\Domain\\Traity',
            'App\\Domain\\Status',
            'App\\Domain\\Product',
        ]);
    });
});

describe('ParsedFile — use statements', function (): void {
    it('indexes plain use statements by their short name', function (): void {
        $parsed = (new PhpParser())->parseString(loadFixture('SimpleClass'));
        $uses = $parsed->useStatements();
        expect($uses)->toHaveKey('UserService', 'App\\Services\\UserService');
        expect($uses)->toHaveKey('Request', 'Illuminate\\Http\\Request');
    });

    it('honours use-as aliases', function (): void {
        $parsed = (new PhpParser())->parseString(loadFixture('SimpleClass'));
        expect($parsed->useStatements())->toHaveKey('UserRepo', 'App\\Contracts\\Repository');
    });

    it('expands grouped use statements', function (): void {
        $parsed = (new PhpParser())->parseString(loadFixture('GroupedUses'));
        $uses = $parsed->useStatements();
        expect($uses)->toHaveKey('User', 'App\\Models\\User');
        expect($uses)->toHaveKey('Order', 'App\\Models\\Order');
        expect($uses)->toHaveKey('Product', 'App\\Models\\Product');
    });
});

describe('ParsedFile — resolveClassName()', function (): void {
    it('resolves an imported class to its FQCN', function (): void {
        $parsed = (new PhpParser())->parseString(loadFixture('SimpleClass'));
        expect($parsed->resolveClassName('UserService'))->toBe('App\\Services\\UserService');
    });

    it('resolves an alias to its target FQCN', function (): void {
        $parsed = (new PhpParser())->parseString(loadFixture('SimpleClass'));
        expect($parsed->resolveClassName('UserRepo'))->toBe('App\\Contracts\\Repository');
    });

    it('strips a leading backslash from absolute references', function (): void {
        $parsed = (new PhpParser())->parseString(loadFixture('SimpleClass'));
        expect($parsed->resolveClassName('\\Some\\Absolute\\Ref'))->toBe('Some\\Absolute\\Ref');
    });

    it('prepends the current namespace to unqualified names', function (): void {
        $parsed = (new PhpParser())->parseString(loadFixture('SimpleClass'));
        expect($parsed->resolveClassName('LocalOnly'))->toBe('App\\Http\\Controllers\\LocalOnly');
    });

    it('resolves an alias with a nested tail', function (): void {
        $parsed = (new PhpParser())->parseString(loadFixture('SimpleClass'));
        expect($parsed->resolveClassName('UserService\\Nested'))->toBe('App\\Services\\UserService\\Nested');
    });
});

describe('ParsedFile — findNodes()', function (): void {
    it('collects nodes of the requested type', function (): void {
        $parsed = (new PhpParser())->parseString(loadFixture('SimpleClass'));
        expect($parsed->findNodes(Class_::class))->toHaveCount(1);
        expect($parsed->findNodes(ClassMethod::class))->toHaveCount(2);
    });
});
