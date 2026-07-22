<?php

declare(strict_types=1);

/*
 * Orchestra Testbench integration tests.
 *
 * These boot a real Laravel skeleton with the CodeAtlasServiceProvider
 * registered, so they require testbench in require-dev:
 *
 *   composer require --dev orchestra/testbench
 *
 * Run: ./vendor/bin/pest packages/laravel --testsuite=laravel
 */

it('registers the codeatlas commands with artisan', function (): void {
    $registered = array_keys($this->app->make(Illuminate\Contracts\Console\Kernel::class)->all());

    expect($registered)->toContain('codeatlas:analyze', 'codeatlas:scan');
});

it('merges default configuration', function (): void {
    expect(config('codeatlas.pretty'))->toBeTrue();
    expect(config('codeatlas.scan_paths'))->toBeNull();
});

it('runs codeatlas:scan against the testbench skeleton', function (): void {
    $this->artisan('codeatlas:scan')
        ->assertSuccessful();
});

it('runs codeatlas:analyze and writes the JSON document', function (): void {
    $output = sys_get_temp_dir() . '/codeatlas-testbench-' . uniqid();

    $this->artisan('codeatlas:analyze', ['--output' => $output])
        ->assertSuccessful();

    $file = $output . '/codeatlas-analysis.json';
    expect(is_file($file))->toBeTrue();

    /** @var array<string, mixed> $doc */
    $doc = json_decode((string) file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
    expect($doc['version'])->toBe('1.0.0');
    expect($doc)->toHaveKeys(['$schema', 'project', 'analysis', 'graph', 'results', 'errors']);

    @unlink($file);
    @rmdir($output);
});

it('honours the --analyzer filter', function (): void {
    $output = sys_get_temp_dir() . '/codeatlas-testbench-' . uniqid();

    $this->artisan('codeatlas:analyze', ['--analyzer' => ['routes'], '--output' => $output])
        ->assertSuccessful();

    $file = $output . '/codeatlas-analysis.json';
    /** @var array<string, mixed> $doc */
    $doc = json_decode((string) file_get_contents($file), true);
    expect($doc['analysis']['analyzers'])->toBe(['routes']);

    @unlink($file);
    @rmdir($output);
});
