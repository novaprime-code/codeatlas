<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Events;

/**
 * Canonical event names dispatched by the pipeline.
 *
 * Analyzers and consumers listen for these to observe pipeline progress
 * without coupling to specific handler classes.
 */
final class Events
{
    public const SCAN_STARTED = 'scan.started';
    public const SCAN_COMPLETED = 'scan.completed';
    public const ANALYSIS_STARTED = 'analysis.started';
    public const ANALYSIS_COMPLETED = 'analysis.completed';
    public const ANALYSIS_ERROR = 'analysis.error';
    public const EXPORT_STARTED = 'export.started';
    public const EXPORT_COMPLETED = 'export.completed';
    public const PIPELINE_STARTED = 'pipeline.started';
    public const PIPELINE_COMPLETED = 'pipeline.completed';

    private function __construct() {}
}
