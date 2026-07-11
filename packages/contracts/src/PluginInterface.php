<?php

declare(strict_types=1);

namespace CodeAtlas\Contracts;

use CodeAtlas\Contracts\Exceptions\PluginException;

/**
 * Entry point for a CodeAtlas plugin (analyzer or exporter package).
 *
 * The plugin loader calls register() during bootstrap; the plugin binds its
 * services into the container and tags them so the pipeline can discover them.
 */
interface PluginInterface
{
    /**
     * @throws PluginException
     */
    public function register(ContainerInterface $container): void;
}
