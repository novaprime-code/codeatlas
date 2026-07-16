<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Plugin;

use CodeAtlas\Contracts\AnalyzerInterface;
use CodeAtlas\Contracts\ContainerInterface;
use CodeAtlas\Contracts\Exceptions\PluginException;
use CodeAtlas\Contracts\ExporterInterface;
use CodeAtlas\Contracts\PluginInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Discovers and registers CodeAtlas plugins.
 *
 * A plugin package (analyzer or exporter) provides a class implementing
 * PluginInterface; that class is responsible for binding its concrete
 * services into the container. The loader also tags the resolved services
 * so the PipelineRunner can enumerate all analyzers and exporters by role.
 *
 * The loader is intentionally passive: consumers pass class-strings
 * explicitly (either directly or read from composer extra), keeping
 * discovery cost predictable and framework-agnostic.
 */
final class PluginLoader
{
    public const TAG_ANALYZER = 'codeatlas.analyzer';
    public const TAG_EXPORTER = 'codeatlas.exporter';

    /** @var list<class-string<PluginInterface>> */
    private array $registered = [];

    public function __construct(private readonly ContainerInterface $container) {}

    /**
     * Register a plugin by its entry-point class.
     *
     * @param class-string<PluginInterface> $pluginClass
     *
     * @throws PluginException
     */
    public function register(string $pluginClass): void
    {
        if (in_array($pluginClass, $this->registered, true)) {
            return;
        }

        if (!class_exists($pluginClass)) {
            throw PluginException::classNotFound($pluginClass);
        }

        try {
            $reflection = new ReflectionClass($pluginClass);
        } catch (ReflectionException) {
            throw PluginException::classNotFound($pluginClass);
        }

        if (!$reflection->implementsInterface(PluginInterface::class)) {
            throw PluginException::doesNotImplementInterface($pluginClass);
        }

        try {
            /** @var PluginInterface $plugin */
            $plugin = $reflection->newInstance();
            $plugin->register($this->container);
        } catch (\Throwable $e) {
            throw PluginException::registrationFailed($pluginClass, $e->getMessage());
        }

        $this->registered[] = $pluginClass;
    }

    /**
     * Register many plugins at once.
     *
     * @param list<class-string<PluginInterface>> $pluginClasses
     */
    public function registerMany(array $pluginClasses): void
    {
        foreach ($pluginClasses as $class) {
            $this->register($class);
        }
    }

    /**
     * Tag an analyzer binding for enumeration by the pipeline.
     *
     * @param class-string<AnalyzerInterface> $analyzerClass
     */
    public function tagAnalyzer(string $analyzerClass): void
    {
        $this->container->tag($analyzerClass, self::TAG_ANALYZER);
    }

    /**
     * Tag an exporter binding for enumeration by the pipeline.
     *
     * @param class-string<ExporterInterface> $exporterClass
     */
    public function tagExporter(string $exporterClass): void
    {
        $this->container->tag($exporterClass, self::TAG_EXPORTER);
    }

    /**
     * @return list<class-string<PluginInterface>>
     */
    public function registered(): array
    {
        return $this->registered;
    }
}
