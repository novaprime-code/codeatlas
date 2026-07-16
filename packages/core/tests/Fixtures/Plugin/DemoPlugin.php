<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Tests\Fixtures\Plugin;

use CodeAtlas\Contracts\ContainerInterface;
use CodeAtlas\Contracts\PluginInterface;
use CodeAtlas\Core\Tests\Fixtures\Container\SimpleService;

final class DemoPlugin implements PluginInterface
{
    public function register(ContainerInterface $container): void
    {
        $container->singleton(SimpleService::class, SimpleService::class);
    }
}
