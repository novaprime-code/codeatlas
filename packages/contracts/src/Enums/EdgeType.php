<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts\Enums;

/**
 * Every kind of relationship between two graph nodes.
 */
enum EdgeType: string
{
    case RoutesTo = 'routes_to';
    case Calls = 'calls';
    case DependsOn = 'depends_on';
    case Extends = 'extends';
    case Implements = 'implements';
    case UsesTrait = 'uses_trait';
    case UsesMiddleware = 'uses_middleware';
    case HasRelationship = 'has_relationship';
    case Dispatches = 'dispatches';
    case ListensTo = 'listens_to';
    case Queues = 'queues';
    case Notifies = 'notifies';
    case Authorizes = 'authorizes';
    case Schedules = 'schedules';
    case Migrates = 'migrates';
}
