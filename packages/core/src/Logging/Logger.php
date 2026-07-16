<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Logging;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;

/**
 * PSR-3 compliant logger with configurable minimum level and target stream.
 *
 * Writes to stderr by default. A file path can be provided; the logger opens
 * the handle lazily and appends. Context placeholders {key} are interpolated
 * into the message per PSR-3 §1.2.
 */
final class Logger extends AbstractLogger
{
    /** @var array<string, int> */
    private const LEVEL_WEIGHTS = [
        LogLevel::DEBUG => 100,
        LogLevel::INFO => 200,
        LogLevel::NOTICE => 250,
        LogLevel::WARNING => 300,
        LogLevel::ERROR => 400,
        LogLevel::CRITICAL => 500,
        LogLevel::ALERT => 550,
        LogLevel::EMERGENCY => 600,
    ];

    private ?LoggerSink $sink;

    public function __construct(
        private readonly string $minLevel = LogLevel::DEBUG,
        ?LoggerSink $sink = null,
    ) {
        $this->sink = $sink ?? new StderrSink();
    }

    public static function toFile(string $path, string $minLevel = LogLevel::DEBUG): self
    {
        return new self($minLevel, new FileSink($path));
    }

    public static function null(): self
    {
        return new self(LogLevel::EMERGENCY, new NullSink());
    }

    /**
     * @param array<string, mixed> $context
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $levelKey = is_string($level) ? $level : LogLevel::INFO;
        $weight = self::LEVEL_WEIGHTS[$levelKey] ?? 0;
        $threshold = self::LEVEL_WEIGHTS[$this->minLevel] ?? 100;

        if ($weight < $threshold) {
            return;
        }

        $formatted = $this->format($levelKey, (string) $message, $context);
        $this->sink?->write($formatted);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function format(string $level, string $message, array $context): string
    {
        $interpolated = $this->interpolate($message, $context);
        $timestamp = date('Y-m-d H:i:s');

        return sprintf("[%s] [%s] %s\n", $timestamp, strtoupper($level), $interpolated);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function interpolate(string $message, array $context): string
    {
        if ($context === []) {
            return $message;
        }

        $replacements = [];

        foreach ($context as $key => $value) {
            if (is_scalar($value) || $value === null || $value instanceof Stringable) {
                $replacements['{' . $key . '}'] = (string) $value;
            }
        }

        return strtr($message, $replacements);
    }
}
