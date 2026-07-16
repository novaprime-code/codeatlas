<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Enums;

/**
 * Severity level of an analysis error or diagnostic.
 */
enum Severity: string
{
    case Error = 'error';
    case Warning = 'warning';
    case Info = 'info';
    case Debug = 'debug';
}
