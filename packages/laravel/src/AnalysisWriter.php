<?php

declare(strict_types=1);

namespace CodeAtlas\Laravel;

use CodeAtlas\Contracts\Exceptions\ExporterException;
use CodeAtlas\Core\Pipeline\PipelineResult;

/**
 * Framework-free disk writer shared by the artisan commands.
 *
 * Kept out of the command classes so it is testable without booting
 * Laravel. Project metadata injection into exports is handled by the
 * PipelineRunner itself; this class only persists the outputs.
 */
final class AnalysisWriter
{
    /**
     * Write every export in a PipelineResult to a directory.
     *
     * @return list<string> Written absolute file paths
     *
     * @throws ExporterException
     */
    public static function write(PipelineResult $result, string $directory): array
    {
        if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw ExporterException::writeFailed($directory);
        }

        $written = [];

        foreach ($result->exports as $output) {
            $path = rtrim($directory, '/') . '/' . $output->filename;

            if (@file_put_contents($path, $output->content) === false) {
                throw ExporterException::writeFailed($path);
            }

            $written[] = $path;
        }

        return $written;
    }
}
