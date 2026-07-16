<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Exceptions\AnalyzerException;
use CodeAtlas\Contracts\Exceptions\CodeAtlasException;
use CodeAtlas\Contracts\Exceptions\ConfigurationException;
use CodeAtlas\Contracts\Exceptions\ContainerException;
use CodeAtlas\Contracts\Exceptions\ExporterException;
use CodeAtlas\Contracts\Exceptions\ParserException;
use CodeAtlas\Contracts\Exceptions\PluginException;
use CodeAtlas\Contracts\Exceptions\ScannerException;

describe('exception hierarchy', function (): void {
    it('roots every concrete exception under CodeAtlasException', function (): void {
        expect(ScannerException::pathNotFound('/x'))->toBeInstanceOf(CodeAtlasException::class);
        expect(ParserException::fileNotReadable('/x'))->toBeInstanceOf(CodeAtlasException::class);
        expect(AnalyzerException::invalidConfiguration('routes', 'r'))->toBeInstanceOf(CodeAtlasException::class);
        expect(ExporterException::encodingFailed('json', 'r'))->toBeInstanceOf(CodeAtlasException::class);
        expect(ConfigurationException::missingKey('x'))->toBeInstanceOf(CodeAtlasException::class);
        expect(PluginException::classNotFound('X'))->toBeInstanceOf(CodeAtlasException::class);
        expect(ContainerException::notBound('X'))->toBeInstanceOf(CodeAtlasException::class);
    });

    it('named constructors produce descriptive messages', function (): void {
        expect(ScannerException::pathNotFound('/nope')->getMessage())->toContain('/nope');
        expect(ParserException::syntaxError('/x.php', 'oops')->getMessage())->toContain('/x.php');
        expect(ContainerException::circularDependency('Foo')->getMessage())->toContain('Foo');
    });
});
