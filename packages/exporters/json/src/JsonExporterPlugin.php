<?php

declare(strict_types=1);

namespace CodeAtlas\Exporters\Json;

use CodeAtlas\Contracts\ContainerInterface;
use CodeAtlas\Contracts\PluginInterface;

/**
 * Registers the JSON exporter into the CodeAtlas container.
 */
final class JsonExporterPlugin implements PluginInterface
{
    public function register(ContainerInterface $container): void
    {
        $container->singleton(JsonExporter::class, JsonExporter::class);
    }
}
