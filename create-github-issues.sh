#!/bin/bash

# =============================================================================
# CodeAtlas — GitHub Issues Creator
# =============================================================================
# Usage: ./create-github-issues.sh YOUR_ORG/codeatlas
#
# Prerequisites:
#   - GitHub CLI (gh) installed and authenticated
#   - Labels and milestones already created (see PROJECT_MANAGEMENT_SETUP.md)
#
# This script creates all issues in dependency order.
# Run sections one at a time to avoid GitHub API rate limits.
# =============================================================================

REPO="${1:-YOUR_ORG/codeatlas}"

echo "Creating issues for $REPO"
echo "========================="
echo ""

# Helper function
create_issue() {
    local title="$1"
    local body="$2"
    local labels="$3"
    local milestone="$4"

    echo "Creating: $title"
    gh issue create \
        --repo "$REPO" \
        --title "$title" \
        --body "$body" \
        --label "$labels" \
        --milestone "$milestone" \
        2>/dev/null

    # Rate limit protection
    sleep 1
}

# =============================================================================
# PHASE 0 — INFRASTRUCTURE
# =============================================================================

echo ""
echo "=== Phase 0 — Infrastructure ==="
echo ""

create_issue \
    "[Epic] Phase 0 — Infrastructure" \
    "## Overview
Repository setup, CI/CD, tooling configuration. Zero features, zero business logic.

## Goal
A repository where \`make test\`, \`make lint\`, \`make analyze\` all pass with zero features. Every package has a skeleton with passing CI.

## Sub-tasks
All tasks prefixed with \`[Task]\` and labeled \`phase:0-infra\` are children of this epic.

## Acceptance Criteria
- [ ] All PHP packages have skeleton with \`composer.json\` and PSR-4 autoload
- [ ] Frontend app skeleton runs with Vite + React + TypeScript
- [ ] Pest, PHPStan, Pint, ESLint all configured and passing
- [ ] GitHub Actions CI runs on push and PR
- [ ] Conventional commits enforced via Commitlint
- [ ] Makefile provides all common commands" \
    "type:epic,phase:0-infra,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Initialize root composer.json with monorepo config" \
    "## Description
Create root \`composer.json\` with \`type: project\`. Configure path repositories for all packages. Set minimum-stability, PHP requirement, and scripts.

## Technical Notes
- Use \`repositories\` array with \`type: path\` for each package
- Require \`php: ^8.3\`
- Set \`minimum-stability: stable\`
- Add scripts: \`test\`, \`lint\`, \`analyze\`

## Acceptance Criteria
- [ ] \`composer install\` succeeds with all package paths resolved
- [ ] All package namespaces autoload correctly
- [ ] Scripts run without errors" \
    "type:task,phase:0-infra,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Create root package.json and PNPM workspace config" \
    "## Description
Create root \`package.json\` and \`pnpm-workspace.yaml\` pointing to \`apps/*\` and \`packages/ui/*\`.

## Technical Notes
- Set \`packageManager: pnpm@9.x\`
- Workspace packages: \`apps/*\`, \`packages/ui\`

## Acceptance Criteria
- [ ] \`pnpm install\` succeeds
- [ ] Workspaces resolve correctly" \
    "type:task,phase:0-infra,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Configure Turborepo for monorepo orchestration" \
    "## Description
Create \`turbo.json\` with \`build\`, \`test\`, \`lint\`, and \`analyze\` pipelines. Configure proper dependency graph between tasks.

## Acceptance Criteria
- [ ] \`turbo run test\` runs all packages in correct order
- [ ] \`turbo run build\` builds frontend
- [ ] \`turbo run lint\` lints all packages" \
    "type:task,phase:0-infra,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Create Makefile with common commands" \
    "## Description
Create Makefile with targets: \`install\`, \`test\`, \`test-php\`, \`test-frontend\`, \`lint\`, \`lint-php\`, \`lint-frontend\`, \`analyze\`, \`format\`, \`format-php\`, \`format-frontend\`, \`build\`, \`clean\`, \`help\`.

## Acceptance Criteria
- [ ] \`make help\` shows all targets with descriptions
- [ ] Each target runs the correct underlying command" \
    "type:task,phase:0-infra,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Create .gitignore, .editorconfig, LICENSE" \
    "## Description
- \`.gitignore\`: vendor/, node_modules/, .env, .idea/, dist/, build/, coverage/, caches
- \`.editorconfig\`: spaces, 4 for PHP, 2 for TS/JSON/YAML, utf-8, lf
- \`LICENSE\`: MIT with current year

## Acceptance Criteria
- [ ] No build artifacts tracked
- [ ] Editor settings apply in VS Code
- [ ] MIT license present" \
    "type:chore,phase:0-infra,P2-medium" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Configure Laravel Pint (PER preset)" \
    "## Description
Create \`pint.json\` at root with PER Coding Style 2.0 preset. Add rules for trailing commas, blank line before return, import ordering.

## Acceptance Criteria
- [ ] \`vendor/bin/pint --test\` runs without config errors
- [ ] PER style enforced" \
    "type:task,phase:0-infra,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Configure PHPStan (level max)" \
    "## Description
Create \`phpstan.neon.dist\` at root. Set level: max. Configure paths for all \`packages/*/src\`.

## Acceptance Criteria
- [ ] \`vendor/bin/phpstan analyse\` runs without config errors
- [ ] Level max enforced across all packages" \
    "type:task,phase:0-infra,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Configure Rector for PHP 8.3" \
    "## Description
Create \`rector.php\` with PHP 8.3 target. Add quality rules: readonly properties, constructor promotion, match expressions.

## Acceptance Criteria
- [ ] \`vendor/bin/rector process --dry-run\` runs without errors" \
    "type:task,phase:0-infra,P2-medium" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Configure Pest testing framework" \
    "## Description
Install Pest. Create \`phpunit.xml.dist\` with test suites per package. Create root \`Pest.php\` config.

## Acceptance Criteria
- [ ] \`vendor/bin/pest\` runs (0 tests, 0 assertions OK)
- [ ] Test suites configured per package" \
    "type:task,phase:0-infra,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Create contracts package skeleton" \
    "## Description
Create \`packages/contracts/\` with:
- \`composer.json\` (\`codeatlas/contracts\`, zero dependencies)
- PSR-4 autoload: \`CodeAtlas\\\\Contracts\\\\\` → \`src/\`
- \`src/\` directory
- \`tests/Pest.php\`
- \`README.md\`

## Acceptance Criteria
- [ ] Namespace resolves correctly
- [ ] Pest runs in this package (0 tests OK)" \
    "type:task,phase:0-infra,pkg:contracts,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Create core package skeleton" \
    "## Description
Create \`packages/core/\` with:
- \`composer.json\` (\`codeatlas/core\`, requires \`codeatlas/contracts\`)
- PSR-4 autoload: \`CodeAtlas\\\\Core\\\\\` → \`src/\`
- Requires: \`nikic/php-parser ^5.0\`, \`psr/log ^3.0\`, \`symfony/finder ^7.0\`

## Acceptance Criteria
- [ ] Namespace resolves correctly
- [ ] Dependencies install" \
    "type:task,phase:0-infra,pkg:core,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Create scanner package skeleton" \
    "## Description
Create \`packages/scanner/\` with \`composer.json\` requiring \`codeatlas/contracts\` and \`codeatlas/core\`.

## Acceptance Criteria
- [ ] Namespace \`CodeAtlas\\\\Scanner\\\\\` resolves
- [ ] Pest runs in this package" \
    "type:task,phase:0-infra,pkg:scanner,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Create Laravel bridge package skeleton" \
    "## Description
Create \`packages/laravel/\` with \`composer.json\` requiring \`codeatlas/core\`, \`codeatlas/scanner\`, and \`illuminate/*\` packages. Include empty \`config/codeatlas.php\`.

## Acceptance Criteria
- [ ] Namespace \`CodeAtlas\\\\Laravel\\\\\` resolves
- [ ] Pest runs in this package" \
    "type:task,phase:0-infra,pkg:laravel,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Create route analyzer package skeleton" \
    "## Description
Create \`packages/analyzers/routes/\` with \`composer.json\` requiring \`codeatlas/contracts\` and \`codeatlas/core\`.

## Acceptance Criteria
- [ ] Namespace \`CodeAtlas\\\\Analyzers\\\\Routes\\\\\` resolves
- [ ] Pest runs in this package" \
    "type:task,phase:0-infra,pkg:analyzer-routes,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Create JSON exporter package skeleton" \
    "## Description
Create \`packages/exporters/json/\` with \`composer.json\` requiring \`codeatlas/contracts\` and \`codeatlas/core\`.

## Acceptance Criteria
- [ ] Namespace \`CodeAtlas\\\\Exporters\\\\Json\\\\\` resolves
- [ ] Pest runs in this package" \
    "type:task,phase:0-infra,pkg:exporter-json,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Initialize React + TypeScript frontend with Vite" \
    "## Description
Create \`apps/web/\` with Vite + React + TypeScript. Strict mode enabled. Path aliases configured.

Install: Tailwind CSS, shadcn/ui, React Flow, Monaco Editor, TanStack Query, Zustand

## Acceptance Criteria
- [ ] \`pnpm dev\` starts dev server
- [ ] \`pnpm build\` produces production build
- [ ] TypeScript strict mode enabled
- [ ] All dependencies installed" \
    "type:task,phase:0-infra,pkg:ui,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Configure ESLint + Prettier for frontend" \
    "## Description
Configure ESLint with \`@typescript-eslint\`, \`react-hooks\`, no-\`any\` rule, import ordering.
Configure Prettier: semi, singleQuote, trailingComma all, tabWidth 2, printWidth 100.

## Acceptance Criteria
- [ ] \`pnpm lint\` passes
- [ ] \`pnpm format\` formats correctly" \
    "type:task,phase:0-infra,pkg:ui,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Create CI workflow — PHP + Frontend pipeline" \
    "## Description
Create \`.github/workflows/ci.yml\`:
- PHP matrix: 8.3, 8.4
- PHP steps: composer install, pint --test, phpstan, pest
- Frontend steps: pnpm install, eslint, vitest, vite build
- Triggers: push to main, PRs

## Acceptance Criteria
- [ ] CI runs on push and PRs
- [ ] All checks pass on skeleton repo" \
    "type:task,phase:0-infra,P1-high" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Create GitHub Issue + PR Templates" \
    "## Description
Create \`.github/ISSUE_TEMPLATE/\` with bug.yml, feature.yml, task.yml.
Create \`.github/PULL_REQUEST_TEMPLATE.md\`.

## Acceptance Criteria
- [ ] Templates render correctly on GitHub" \
    "type:task,phase:0-infra,P2-medium" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Configure Husky + lint-staged + Commitlint" \
    "## Description
- Husky: pre-commit and commit-msg hooks
- lint-staged: Pint on .php, ESLint on .ts/.tsx
- Commitlint: conventional commit rules

## Acceptance Criteria
- [ ] Non-conventional commits rejected
- [ ] Staged files auto-formatted on commit" \
    "type:task,phase:0-infra,P2-medium" \
    "v0.0.1 — Foundation"

create_issue \
    "[Task] Create initial README.md" \
    "## Description
Minimal README: project name, one-line description, 'Under Development' badge, tech stack, license.

## Acceptance Criteria
- [ ] README exists with project overview" \
    "type:docs,phase:0-infra,P2-medium" \
    "v0.0.1 — Foundation"

# =============================================================================
# PHASE 1 — CONTRACTS
# =============================================================================

echo ""
echo "=== Phase 1 — Contracts ==="
echo ""

create_issue \
    "[Epic] Phase 1 — Contracts Package" \
    "## Overview
All interfaces, enums, value objects, and exception classes that define the public API. Zero-dependency package.

## Goal
Every other package can implement these contracts without circular dependencies.

## Acceptance Criteria
- [ ] All interfaces defined with PHPDoc
- [ ] All enums created with backed values
- [ ] All value objects are readonly with toArray/fromArray
- [ ] Exception hierarchy established
- [ ] 100% test coverage
- [ ] PHPStan level max passes" \
    "type:epic,phase:1-core,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement AnalyzerInterface" \
    "## Description
\`\`\`php
interface AnalyzerInterface
{
    public function name(): string;
    public function supportedNodeTypes(): array;
    public function analyze(ProjectContext \$context): AnalysisResult;
}
\`\`\`

## Acceptance Criteria
- [ ] Interface exists at \`CodeAtlas\\Contracts\\AnalyzerInterface\`
- [ ] PHPStan passes
- [ ] PHPDoc complete" \
    "type:task,phase:1-core,pkg:contracts,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement ScannerInterface" \
    "## Description
\`\`\`php
interface ScannerInterface
{
    public function scan(string \$path, ?ScanConfig \$config = null): ProjectContext;
}
\`\`\`

## Acceptance Criteria
- [ ] Interface exists
- [ ] PHPStan passes" \
    "type:task,phase:1-core,pkg:contracts,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement ExporterInterface" \
    "## Description
\`\`\`php
interface ExporterInterface
{
    public function name(): string;
    public function export(AnalysisResult \$result, ExportConfig \$config): ExportOutput;
}
\`\`\`

## Acceptance Criteria
- [ ] Interface exists
- [ ] PHPStan passes" \
    "type:task,phase:1-core,pkg:contracts,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Implement Node/Edge/Graph interfaces and value objects" \
    "## Description
Create NodeInterface, EdgeInterface, GraphInterface and their default value object implementations (Node, Edge, Graph). All readonly. Include toArray/fromArray.

## Acceptance Criteria
- [ ] Interfaces exist with full method signatures
- [ ] Value objects implement interfaces
- [ ] toArray/fromArray roundtrip correctly
- [ ] Graph supports merge and duplicate detection
- [ ] Tests pass" \
    "type:task,phase:1-core,pkg:contracts,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Create all enums (NodeType, EdgeType, FileType, Severity)" \
    "## Description
Create backed string enums with all cases defined in JSON_SCHEMA.md.

## Acceptance Criteria
- [ ] NodeType: 20+ cases covering all entity types
- [ ] EdgeType: 15+ cases covering all relationship types
- [ ] FileType: 18+ cases covering all file categories
- [ ] Severity: Error, Warning, Info, Debug
- [ ] All are backed string enums
- [ ] PHPStan passes" \
    "type:task,phase:1-core,pkg:contracts,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Create ProjectContext and AnalysisResult value objects" \
    "## Description
Readonly classes with static factory methods. ProjectContext holds discovered files. AnalysisResult holds nodes/edges from an analyzer.

## Acceptance Criteria
- [ ] ProjectContext: fromPath(), filesOfType(), file counts
- [ ] AnalysisResult: analyzer name, nodes, edges, errors, merge()
- [ ] Both are readonly with toArray()
- [ ] Tests pass" \
    "type:task,phase:1-core,pkg:contracts,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Create exception hierarchy" \
    "## Description
Base CodeAtlasException plus: ScannerException, ParserException, AnalyzerException, ExporterException, ConfigurationException, PluginException. Each with static factory methods for common cases.

## Acceptance Criteria
- [ ] All exceptions exist and extend base
- [ ] Named constructors for common error cases
- [ ] PHPStan passes" \
    "type:task,phase:1-core,pkg:contracts,P1-high" \
    "v0.1.0 — First Light"

create_issue \
    "[Task] Write comprehensive contracts tests" \
    "## Description
Test all value objects, enums, and Graph behavior. Target 100% coverage.

## Acceptance Criteria
- [ ] All value objects tested (construction, toArray, fromArray, edge cases)
- [ ] All enums tested (cases exist, backed values)
- [ ] Graph tested (add, merge, duplicates)
- [ ] Coverage > 95%" \
    "type:test,phase:1-core,pkg:contracts,P1-high" \
    "v0.1.0 — First Light"

echo ""
echo "=== Issue creation complete ==="
echo ""
echo "Next: Create Phase 1 Core, Phase 2 Scanner, Phase 3+ issues"
echo "Run each section separately to manage GitHub API rate limits."
echo ""
echo "Total issues created in this run: ~30"
echo "Remaining issues in TICKETS_BACKLOG.csv: ~120"
