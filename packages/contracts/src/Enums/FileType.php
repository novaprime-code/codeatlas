<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Enums;

/**
 * Classification of a discovered project file, assigned by the scanner
 * based on path conventions.
 */
enum FileType: string
{
    case Route = 'route';
    case Controller = 'controller';
    case Middleware = 'middleware';
    case Service = 'service';
    case Repository = 'repository';
    case Model = 'model';
    case Event = 'event';
    case Listener = 'listener';
    case Job = 'job';
    case Notification = 'notification';
    case Policy = 'policy';
    case Command = 'command';
    case Migration = 'migration';
    case Factory = 'factory';
    case Seeder = 'seeder';
    case Provider = 'provider';
    case Config = 'config';
    case View = 'view';
    case Other = 'other';
}
