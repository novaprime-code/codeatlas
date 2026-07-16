<?php

declare(strict_types=1);

use CodeAtlas\Contracts\Exceptions\PluginException;
use CodeAtlas\Core\Container\Container;
use CodeAtlas\Core\Plugin\PluginLoader;
use CodeAtlas\Core\Tests\Fixtures\Container\SimpleService;
use CodeAtlas\Core\Tests\Fixtures\Plugin\DemoPlugin;

describe('PluginLoader', function (): void {
    it('registers a plugin and binds its services', function (): void {
        $container = new Container();
        $loader = new PluginLoader($container);
        $loader->register(DemoPlugin::class);

        expect($loader->registered())->toContain(DemoPlugin::class);
        expect($container->make(SimpleService::class))->toBeInstanceOf(SimpleService::class);
    });

    it('is idempotent — same plugin registered twice counts once', function (): void {
        $container = new Container();
        $loader = new PluginLoader($container);
        $loader->register(DemoPlugin::class);
        $loader->register(DemoPlugin::class);
        expect($loader->registered())->toHaveCount(1);
    });

    it('throws when the plugin class does not exist', function (): void {
        (new PluginLoader(new Container()))->register('CodeAtlas\\Ghost');
    })->throws(PluginException::class);

    it('throws when the class does not implement PluginInterface', function (): void {
        (new PluginLoader(new Container()))->register(stdClass::class);
    })->throws(PluginException::class);

    it('tags analyzers and exporters for pipeline enumeration', function (): void {
        $container = new Container();
        $loader = new PluginLoader($container);
        $container->bind(SimpleService::class, SimpleService::class);
        $loader->tagAnalyzer(SimpleService::class);
        expect($container->tagged(PluginLoader::TAG_ANALYZER))->toHaveCount(1);
    });
});
