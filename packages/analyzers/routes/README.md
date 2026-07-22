# codeatlas/analyzer-routes

Route analyzer for CodeAtlas. Extracts every route from a Laravel project **via AST** (never regex, per the constitution) and emits graph nodes and edges.

## What it extracts

| Feature | Support |
|---|---|
| HTTP verbs | `get`, `post`, `put`, `patch`, `delete`, `options`, `any` |
| `match()` | Multiple verbs per route |
| Actions | `[Controller::class, 'method']`, `Controller::class` (invokable), `'Controller@method'`, closures |
| Route names | `->name()` + group name prefixes |
| Middleware | `->middleware()` (string + array) + group inheritance |
| Constraints | `->where()`, `->whereNumber()`, `->whereAlpha()`, `->whereAlphaNumeric()`, `->whereUuid()` |
| Domains | `->domain()` + group domains |
| URI parameters | `{id}`, `{slug?}` extraction |
| Groups (fluent) | `Route::prefix()->middleware()->group(fn)` |
| Groups (array) | `Route::group(['prefix' => ..., 'as' => ..., 'middleware' => ...], fn)` |
| Nested groups | Prefixes concatenate, middleware accumulates, names concatenate |
| `resource()` | Expands to 7 routes (index/create/store/show/edit/update/destroy) |
| `apiResource()` | Expands to 5 routes (no create/edit) |

## Output

For each route:
- one **Route node** (`NodeType::Route`) with full metadata per `JSON_SCHEMA.md`
- one **Route → Controller edge** (`EdgeType::RoutesTo`) when the handler is a controller
- one **Route → Middleware edge** (`EdgeType::UsesMiddleware`) per applied middleware

## Fault isolation

A malformed route file is logged, recorded as an `AnalysisError` (warning severity), and skipped — the remaining files are still analyzed. This is the constitution's per-file fault-isolation guarantee.

## Architecture

```
RouteAnalyzer
  └─ RouteExtractor        (context-aware AST descent, group stack)
       ├─ ChainUnwinder    (flattens fluent chains → ordered operations)
       ├─ ValueResolver    (extracts string/array/class-const literals)
       └─ ActionResolver   (controller | invokable | closure | string@method)
```

## Installation

```bash
composer require codeatlas/analyzer-routes
```

Part of the [CodeAtlas](https://github.com/novaprime-code/codeatlas) monorepo. MIT © Snova Labs.
