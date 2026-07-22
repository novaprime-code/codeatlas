<?php

declare(strict_types=1);

namespace CodeAtlas\Analyzers\Routes;

use CodeAtlas\Contracts\ContainerInterface;
use CodeAtlas\Contracts\ParserInterface;
use CodeAtlas\Contracts\PluginInterface;

/**
 * Registers the route analyzer into the CodeAtlas container.
 */
final class RouteAnalyzerPlugin implements PluginInterface
{
    public function register(ContainerInterface $container): void
    {
        $container->singleton(RouteAnalyzer::class, static function (ContainerInterface $c): RouteAnalyzer {
            return new RouteAnalyzer($c->make(ParserInterface::class));
        });
    }
}
