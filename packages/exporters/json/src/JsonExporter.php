<?php

declare(strict_types=1);

namespace CodeAtlas\Exporters\Json;

use CodeAtlas\Contracts\Exceptions\ExporterException;
use CodeAtlas\Contracts\ExporterInterface;
use CodeAtlas\Contracts\Graph\EdgeInterface;
use CodeAtlas\Contracts\Graph\NodeInterface;
use CodeAtlas\Contracts\ValueObjects\AnalysisError;
use CodeAtlas\Contracts\ValueObjects\AnalysisResult;
use CodeAtlas\Contracts\ValueObjects\ExportConfig;
use CodeAtlas\Contracts\ValueObjects\ExportOutput;
use DateTimeImmutable;
use DateTimeInterface;
use JsonException;
use stdClass;

/**
 * The canonical JSON exporter.
 *
 * Produces the single JSON document defined in JSON_SCHEMA.md — the ONLY
 * contract between the PHP backend and the TypeScript UI. Nothing else
 * crosses the boundary (constitution: "JSON is the only contract").
 *
 * Document shape:
 *   $schema   — schema URL with version
 *   version   — schema semver, stamped on every export
 *   project   — name/path/framework/framework_version/php_version
 *   analysis  — timestamp/duration_ms/analyzers
 *   graph     — nodes[] + edges[] (what React Flow renders)
 *   results   — per-analyzer result blocks, keyed by analyzer name
 *   errors    — flat list of AnalysisError records
 *
 * Project metadata is not part of AnalysisResult, so callers pass it via
 * ExportConfig::$options['project'] (the Laravel bridge does this from
 * the ProjectContext). Missing keys degrade to null — the document is
 * always structurally complete.
 */
final class JsonExporter implements ExporterInterface
{
    public const SCHEMA_VERSION = '1.0.0';
    public const SCHEMA_URL = 'https://codeatlas.dev/schema/v1/analysis.json';

    /**
     * Analyzer name used by the PipelineRunner for merged results;
     * its metadata is already a per-analyzer map.
     */
    private const MERGED_ANALYZER = 'pipeline';

    public function name(): string
    {
        return 'json';
    }

    public function export(AnalysisResult $result, ExportConfig $config): ExportOutput
    {
        $document = [
            '$schema' => self::SCHEMA_URL,
            'version' => self::SCHEMA_VERSION,
            'project' => $this->projectBlock($config),
            'analysis' => $this->analysisBlock($result, $config),
            'graph' => $this->graphBlock($result),
            'results' => $this->resultsBlock($result),
            'errors' => $this->errorsBlock($result),
        ];

        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR;
        if ($config->prettyPrint) {
            $flags |= JSON_PRETTY_PRINT;
        }

        try {
            $json = json_encode($document, $flags);
        } catch (JsonException $e) {
            throw ExporterException::encodingFailed($this->name(), $e->getMessage());
        }

        return new ExportOutput(
            content: $json,
            mimeType: 'application/json',
            filename: 'codeatlas-analysis.json',
        );
    }

    /**
     * @return array{name: ?string, path: ?string, framework: ?string, framework_version: ?string, php_version: ?string}
     */
    private function projectBlock(ExportConfig $config): array
    {
        $project = $config->options['project'] ?? [];
        if (!is_array($project)) {
            $project = [];
        }

        return [
            'name' => $this->stringOrNull($project['name'] ?? null),
            'path' => $this->stringOrNull($project['path'] ?? null),
            'framework' => $this->stringOrNull($project['framework'] ?? null),
            'framework_version' => $this->stringOrNull($project['framework_version'] ?? null),
            'php_version' => $this->stringOrNull($project['php_version'] ?? null),
        ];
    }

    /**
     * @return array{timestamp: string, duration_ms: int, analyzers: list<string>}
     */
    private function analysisBlock(AnalysisResult $result, ExportConfig $config): array
    {
        $durationOption = $config->options['duration_ms'] ?? null;
        $duration = is_int($durationOption) ? $durationOption : 0;

        return [
            'timestamp' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM),
            'duration_ms' => $duration,
            'analyzers' => array_keys($this->resultsBlock($result)),
        ];
    }

    /**
     * @return array{nodes: list<array<string, mixed>>, edges: list<array<string, mixed>>}
     */
    private function graphBlock(AnalysisResult $result): array
    {
        return [
            'nodes' => array_map(
                fn(NodeInterface $node): array => $this->normalizeNode($node->toArray()),
                $result->nodes,
            ),
            'edges' => array_map(
                fn(EdgeInterface $edge): array => $this->normalizeEdge($edge->toArray()),
                $result->edges,
            ),
        ];
    }

    /**
     * PHP serializes empty associative arrays as JSON arrays ([]), but the
     * schema types metadata and where as objects ({}). TypeScript strict
     * mode rejects [] where Record<string, T> is expected, so empty maps
     * are coerced to stdClass before encoding.
     *
     * @param array<string, mixed> $node
     *
     * @return array<string, mixed>
     */
    private function normalizeNode(array $node): array
    {
        $node['metadata'] = $this->mapOrObject($node['metadata'] ?? []);
        if (is_array($node['metadata'])) {
            foreach (['where'] as $mapKey) {
                if (isset($node['metadata'][$mapKey]) && $node['metadata'][$mapKey] === []) {
                    $node['metadata'][$mapKey] = new stdClass();
                }
            }
        }

        return $node;
    }

    /**
     * @param array<string, mixed> $edge
     *
     * @return array<string, mixed>
     */
    private function normalizeEdge(array $edge): array
    {
        $edge['metadata'] = $this->mapOrObject($edge['metadata'] ?? []);

        return $edge;
    }

    private function mapOrObject(mixed $value): mixed
    {
        return $value === [] ? new stdClass() : $value;
    }

    /**
     * Per-analyzer result blocks keyed by analyzer name.
     *
     * A merged pipeline result already carries [analyzer => metadata];
     * a single-analyzer result is wrapped under its own name.
     *
     * @return array<string, mixed>
     */
    private function resultsBlock(AnalysisResult $result): array
    {
        if ($result->analyzer === self::MERGED_ANALYZER) {
            /** @var array<string, mixed> $metadata */
            $metadata = $result->metadata;

            return $metadata;
        }

        return [$result->analyzer => $result->metadata];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function errorsBlock(AnalysisResult $result): array
    {
        return array_map(
            static fn(AnalysisError $error): array => $error->toArray(),
            $result->errors,
        );
    }

    private function stringOrNull(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }
}
