<?php

declare(strict_types=1);

use CodeAtlas\Exporters\Json\JsonExporter;
use CodeAtlas\Laravel\AnalysisWriter;
use CodeAtlas\Laravel\CodeAtlasFactory;

describe('AnalysisWriter', function (): void {
    it('writes exports to disk and creates directories on demand', function (): void {
        ['runner' => $runner] = CodeAtlasFactory::make();
        $appPath = dirname(__DIR__, 3) . '/analyzers/routes/tests/Fixtures/integration-app';
        $result = $runner->run(projectPath: $appPath, exporters: [JsonExporter::class]);

        $dir = sys_get_temp_dir() . '/codeatlas-writer-' . uniqid() . '/nested';
        try {
            $written = AnalysisWriter::write($result, $dir);

            expect($written)->toHaveCount(1);
            expect($written[0])->toEndWith('codeatlas-analysis.json');
            expect(is_file($written[0]))->toBeTrue();

            /** @var array<string, mixed> $doc */
            $doc = json_decode((string) file_get_contents($written[0]), true);
            expect($doc['version'])->toBe('1.0.0');
        } finally {
            foreach (glob($dir . '/*') ?: [] as $f) {
                @unlink($f);
            }
            @rmdir($dir);
            @rmdir(dirname($dir));
        }
    });
});
