# CodeAtlas Coding Standards

## PHP Standards

### Version & Features
- PHP 8.3+ required
- Use constructor promotion everywhere
- Use readonly properties by default
- Use enums instead of class constants for finite sets
- Use named arguments for clarity when calling functions with 3+ parameters
- Use match expressions over switch
- Use first-class callable syntax (`$this->method(...)`)
- Use intersection and union types, never `mixed` unless truly necessary
- Use `readonly class` where all properties are readonly

### Code Style
- Follow PSR-12 with the following additions
- Enforced via Laravel Pint with preset `per` (PER Coding Style 2.0)
- Max line length: 120 characters
- Blank line before return statements
- Trailing commas in multi-line arrays, function arguments, and parameters
- Import every class (no inline `\Namespace\Class`)

### Naming Conventions

| Element | Convention | Example |
|---------|------------|---------|
| Class | PascalCase | `RouteAnalyzer` |
| Interface | PascalCase + `Interface` suffix | `AnalyzerInterface` |
| Abstract class | `Abstract` prefix | `AbstractAnalyzer` |
| Enum | PascalCase | `NodeType` |
| Enum case | PascalCase | `NodeType::Controller` |
| Method | camelCase | `analyzeRoutes()` |
| Property | camelCase | `$routeCollection` |
| Constant | UPPER_SNAKE | `MAX_DEPTH` |
| Variable | camelCase | `$parsedResult` |
| Config key | snake_case | `scan_paths` |
| Event | PascalCase past tense | `AnalysisCompleted` |
| Exception | PascalCase + `Exception` suffix | `ParserException` |
| DTO | PascalCase + descriptive | `RouteData` |
| Test | PascalCase + `Test` suffix | `RouteAnalyzerTest` |

### File Organization

```
packages/analyzers/routes/
├── composer.json
├── phpstan.neon
├── pint.json
├── rector.php
├── src/
│   ├── RouteAnalyzer.php          ← Main analyzer class
│   ├── DTOs/
│   │   ├── RouteData.php
│   │   └── RouteCollection.php
│   ├── Extractors/
│   │   ├── UriExtractor.php
│   │   └── MiddlewareExtractor.php
│   └── Exceptions/
│       └── RouteAnalyzerException.php
├── tests/
│   ├── Unit/
│   │   ├── RouteAnalyzerTest.php
│   │   └── Extractors/
│   │       └── UriExtractorTest.php
│   ├── Integration/
│   │   └── RouteAnalysisIntegrationTest.php
│   ├── Fixtures/
│   │   └── routes/
│   │       ├── web.php
│   │       └── api.php
│   └── Pest.php
├── benchmarks/
│   └── RouteAnalyzerBench.php
└── README.md
```

### Class Design Rules

1. **Single Responsibility.** One class, one job.
2. **Constructor injection only.** No service locator, no `app()` helper, no static factories (except DTOs).
3. **Final by default.** Mark classes `final` unless designed for extension. If a class needs extension, make it abstract.
4. **Return types on everything.** No implicit returns.
5. **No nullable parameters when avoidable.** Use null objects, default values, or overloaded methods.
6. **DTOs are `readonly class` with named constructors.** Use `static` factory methods like `fromArray()`, `fromNode()`.
7. **No magic methods** except `__construct`. No `__get`, `__set`, `__call`.
8. **Max 200 lines per class.** Extract when approaching this limit.
9. **Max 20 lines per method.** Extract private methods or separate classes.
10. **Max 5 parameters per method.** Use a DTO/config object beyond that.

### DTO Pattern

```php
final readonly class RouteData
{
    public function __construct(
        public string $uri,
        public string $method,
        public ?string $name,
        public ?string $controller,
        public ?string $action,
        public array $middleware,
        public ?string $prefix,
        public ?string $domain,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            uri: $data['uri'],
            method: $data['method'],
            name: $data['name'] ?? null,
            controller: $data['controller'] ?? null,
            action: $data['action'] ?? null,
            middleware: $data['middleware'] ?? [],
            prefix: $data['prefix'] ?? null,
            domain: $data['domain'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'uri' => $this->uri,
            'method' => $this->method,
            'name' => $this->name,
            'controller' => $this->controller,
            'action' => $this->action,
            'middleware' => $this->middleware,
            'prefix' => $this->prefix,
            'domain' => $this->domain,
        ];
    }
}
```

### Analyzer Pattern

```php
final class RouteAnalyzer implements AnalyzerInterface
{
    public function __construct(
        private readonly ParserInterface $parser,
        private readonly LoggerInterface $logger,
    ) {}

    public function name(): string
    {
        return 'routes';
    }

    public function supportedNodeTypes(): array
    {
        return [NodeType::Route];
    }

    public function analyze(ProjectContext $context): AnalysisResult
    {
        $nodes = [];
        $edges = [];

        foreach ($context->filesOfType(FileType::Route) as $file) {
            try {
                $extracted = $this->extractRoutes($file);
                $nodes = [...$nodes, ...$extracted->nodes];
                $edges = [...$edges, ...$extracted->edges];
            } catch (ParserException $e) {
                $this->logger->warning("Failed to parse {$file->path}: {$e->getMessage()}");
            }
        }

        return new AnalysisResult(
            analyzer: $this->name(),
            nodes: $nodes,
            edges: $edges,
        );
    }
}
```

### Exception Pattern

```php
final class RouteAnalyzerException extends AnalyzerException
{
    public static function fileNotReadable(string $path): self
    {
        return new self("Route file is not readable: {$path}");
    }

    public static function invalidRouteDefinition(string $file, int $line): self
    {
        return new self("Invalid route definition in {$file} at line {$line}");
    }
}
```

## TypeScript Standards

### Configuration
- Strict mode: `true` (all strict flags enabled)
- Target: ES2022
- No `any` — use `unknown` and narrow with type guards
- Prefer `interface` for object shapes, `type` for unions/intersections
- Use `as const` for literal types
- Enable `noUncheckedIndexedAccess`

### Naming Conventions

| Element | Convention | Example |
|---------|------------|---------|
| Component | PascalCase | `GraphCanvas.tsx` |
| Hook | camelCase with `use` prefix | `useGraphData.ts` |
| Utility | camelCase | `formatNodeLabel.ts` |
| Type/Interface | PascalCase | `RouteNode` |
| Enum | PascalCase | `NodeType` |
| Constant | UPPER_SNAKE | `MAX_ZOOM_LEVEL` |
| Store | camelCase with `Store` suffix | `graphStore.ts` |
| File | kebab-case for utils, PascalCase for components | `graph-utils.ts`, `GraphCanvas.tsx` |

### Component Structure

```
apps/web/src/
├── components/
│   ├── graph/
│   │   ├── GraphCanvas.tsx
│   │   ├── GraphControls.tsx
│   │   └── nodes/
│   │       ├── RouteNode.tsx
│   │       ├── ControllerNode.tsx
│   │       └── ModelNode.tsx
│   ├── sidebar/
│   │   ├── Sidebar.tsx
│   │   └── SidebarItem.tsx
│   ├── inspector/
│   │   ├── Inspector.tsx
│   │   └── PropertyList.tsx
│   └── ui/               ← shadcn components
│       ├── button.tsx
│       └── input.tsx
├── hooks/
│   ├── useAnalysis.ts
│   └── useGraphLayout.ts
├── stores/
│   ├── graphStore.ts
│   └── uiStore.ts
├── types/
│   ├── analysis.ts
│   └── graph.ts
├── lib/
│   ├── api.ts
│   └── graph-utils.ts
└── App.tsx
```

### React Rules

1. **Functional components only.** No class components.
2. **Named exports** for components, default exports only for pages.
3. **Co-locate tests** next to components: `GraphCanvas.tsx` → `GraphCanvas.test.tsx`.
4. **Extract hooks** when component logic exceeds ~30 lines.
5. **No prop drilling beyond 2 levels.** Use Zustand or context.
6. **Memoize expensive computations** with `useMemo`. Memoize callbacks with `useCallback` only when passed to memoized children.
7. **Use TanStack Query** for all server state. No `useEffect` for data fetching.

### Type Pattern for Analysis Data

```typescript
interface AnalysisNode {
  id: string;
  type: NodeType;
  label: string;
  metadata: Record<string, unknown>;
  position?: { x: number; y: number };
  file?: FileReference;
}

interface AnalysisEdge {
  id: string;
  source: string;
  target: string;
  type: EdgeType;
  label?: string;
  metadata?: Record<string, unknown>;
}

interface AnalysisResult {
  analyzer: string;
  nodes: AnalysisNode[];
  edges: AnalysisEdge[];
  metadata: {
    timestamp: string;
    duration_ms: number;
    files_analyzed: number;
    files_skipped: number;
  };
}
```

## Testing Standards

### PHP (Pest)

- Every package has a `tests/` directory
- Test file mirrors source structure
- Use descriptive `it()` and `test()` descriptions
- Use `expect()` API, not `assert*()`
- Group related tests with `describe()`
- Use fixtures in `tests/Fixtures/`, never generate temp files
- Integration tests in `tests/Integration/`, unit tests in `tests/Unit/`
- Minimum 90% line coverage per package
- Benchmark tests in `benchmarks/` using PHPBench

```php
describe('RouteAnalyzer', function () {
    it('extracts GET routes from web.php', function () {
        $analyzer = new RouteAnalyzer(new PhpParser(), new NullLogger());
        $context = ProjectContext::fromPath(__DIR__ . '/Fixtures/laravel-app');

        $result = $analyzer->analyze($context);

        expect($result->nodes)->toHaveCount(5);
        expect($result->nodes[0]->type)->toBe(NodeType::Route);
    });

    it('skips malformed route files without crashing', function () {
        // ...
    });
});
```

### TypeScript (Vitest)

- Co-located test files: `Component.test.tsx`
- Use `describe` / `it` pattern
- Use React Testing Library for component tests
- Mock API calls with MSW
- Snapshot tests only for complex SVG/graph rendering

## Git Standards

### Branch Naming
- `feat/scanner-base` — new feature
- `fix/route-parser-crash` — bug fix
- `refactor/core-container` — refactoring
- `test/controller-analyzer` — adding tests
- `docs/architecture-update` — documentation
- `chore/ci-pipeline` — tooling/infrastructure

### Commit Messages (Conventional Commits)
```
feat(scanner): add configurable path exclusion
fix(analyzer-routes): handle closure-based routes
test(analyzer-controllers): add trait extraction tests
refactor(core): extract pipeline runner from container
docs(readme): add installation instructions
chore(ci): add PHPStan to GitHub Actions
perf(scanner): use lazy iterator for large projects
```

### PR Rules
- One package per PR (never cross-package changes unless contracts change)
- PR description must reference the task/issue
- All CI must pass
- Tests required for all new code
- Documentation required for public API changes
