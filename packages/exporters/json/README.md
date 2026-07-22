# codeatlas/exporter-json

The canonical JSON exporter — the ONLY contract between the CodeAtlas backend and the UI.

## Output document

```json
{
  "$schema": "https://codeatlas.dev/schema/v1/analysis.json",
  "version": "1.0.0",
  "project":  { "name", "path", "framework", "framework_version", "php_version" },
  "analysis": { "timestamp", "duration_ms", "analyzers" },
  "graph":    { "nodes": [...], "edges": [...] },
  "results":  { "<analyzer>": { ... } },
  "errors":   [ ... ]
}
```

Full field definitions: `JSON_SCHEMA.md` at the repository root.

## Behaviour guarantees

- **Schema version stamped** on every document (`$schema` URL + `version`)
- **Structurally complete** — every top-level block is present even for an empty result; missing project info degrades to `null`, never omitted keys
- **Empty maps serialize as `{}`**, not `[]` — PHP's empty-array ambiguity is normalized so TypeScript `Record<string, T>` types parse cleanly
- **`prettyPrint`** honoured from `ExportConfig`
- Merged pipeline results (`analyzer === 'pipeline'`) pass through as the per-analyzer map; single-analyzer results are wrapped under their own name

## Passing project metadata

`ExporterInterface::export()` receives only the `AnalysisResult`, so project info travels via config:

```php
$config = new ExportConfig(options: [
    'project' => [
        'name' => $context->name,
        'path' => $context->path,
        'framework' => $context->framework,
        'framework_version' => $context->frameworkVersion,
        'php_version' => $context->phpVersion,
    ],
    'duration_ms' => $pipelineResult->durationMs,
]);
```

The Laravel bridge does this automatically.

## Installation

```bash
composer require codeatlas/exporter-json
```

Part of the [CodeAtlas](https://github.com/novaprime-code/codeatlas) monorepo. MIT © Snova Labs.
