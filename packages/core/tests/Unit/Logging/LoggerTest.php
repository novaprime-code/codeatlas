<?php

declare(strict_types=1);

use CodeAtlas\Core\Logging\Logger;
use CodeAtlas\Core\Logging\NullSink;
use Psr\Log\LogLevel;

describe('Logger — output', function (): void {
    it('writes a message via the configured sink', function (): void {
        $sink = new NullSink();
        (new Logger(LogLevel::DEBUG, $sink))->info('hello');
        expect($sink->lines)->toHaveCount(1);
        expect($sink->lines[0])->toContain('INFO', 'hello');
    });

    it('formats the timestamp in the output', function (): void {
        $sink = new NullSink();
        (new Logger(LogLevel::DEBUG, $sink))->info('x');
        expect($sink->lines[0])->toMatch('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/');
    });
});

describe('Logger — level filtering', function (): void {
    it('suppresses messages below the configured minimum level', function (): void {
        $sink = new NullSink();
        $log = new Logger(LogLevel::WARNING, $sink);
        $log->debug('quiet');
        $log->info('quiet');
        $log->warning('loud');
        $log->error('very loud');
        expect($sink->lines)->toHaveCount(2);
    });
});

describe('Logger — context interpolation', function (): void {
    it('replaces {placeholders} with scalar context values', function (): void {
        $sink = new NullSink();
        (new Logger(LogLevel::DEBUG, $sink))->info('Parsing {file}', ['file' => 'routes/api.php']);
        expect($sink->lines[0])->toContain('routes/api.php');
    });

    it('leaves non-scalar context values un-interpolated', function (): void {
        $sink = new NullSink();
        (new Logger(LogLevel::DEBUG, $sink))->info('x {y}', ['y' => ['a', 'b']]);
        expect($sink->lines[0])->toContain('{y}');
    });
});

describe('Logger::null()', function (): void {
    it('constructs without error', function (): void {
        Logger::null()->error('vanishes');
        expect(true)->toBeTrue();
    });
});
