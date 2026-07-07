# Contributing to CodeAtlas

## Development Prerequisites

- PHP 8.3+
- Composer 2.x
- Node.js 20 LTS
- PNPM 9+
- Rust toolchain (for Tauri, only needed for desktop app)
- Git

## Getting Started

```bash
# Clone
git clone https://github.com/YOUR_ORG/codeatlas.git
cd codeatlas

# Install everything
make install

# Run all tests
make test

# Run all linters
make lint

# Run static analysis
make analyze
```

## Development Workflow

1. Pick a task from `TASKS.md` or a GitHub issue
2. Create a branch: `feat/scanner-base`, `fix/route-parser-crash`
3. Implement the task (one package at a time)
4. Write tests (Pest for PHP, Vitest for TS)
5. Run `make check` (lint + analyze + test)
6. Commit with conventional commit message
7. Open a PR against `main`

## Commit Messages

We use [Conventional Commits](https://www.conventionalcommits.org/):

```
feat(scanner): add configurable path exclusion
fix(analyzer-routes): handle closure-based routes
test(analyzer-controllers): add trait extraction tests
refactor(core): extract pipeline runner from container
docs(readme): add installation instructions
chore(ci): add PHPStan to GitHub Actions
perf(scanner): use lazy iterator for large projects
```

Scope is the package name without the `codeatlas/` prefix.

## Definition of Done

Every PR must satisfy ALL of the following:

- [ ] Tests written and passing (Pest / Vitest)
- [ ] PHPStan passes at level max
- [ ] Pint passes (code style)
- [ ] ESLint passes (if frontend code)
- [ ] Documentation updated (README, PHPDoc, TSDoc)
- [ ] No `any` types in TypeScript
- [ ] No `mixed` types in PHP (unless justified)
- [ ] Benchmark added (if performance-relevant)
- [ ] CI green
- [ ] Single package scope (no cross-package changes unless contracts change)

## Package Development

### Creating a New Analyzer

1. Create directory: `packages/analyzers/{name}/`
2. Create `composer.json` requiring `codeatlas/contracts` and `codeatlas/core`
3. Implement `AnalyzerInterface`
4. Create DTOs in `src/DTOs/`
5. Create tests in `tests/` with fixtures in `tests/Fixtures/`
6. Add benchmark in `benchmarks/`
7. Register in the root `composer.json` repository list
8. Write `README.md`

### Testing

```bash
# All PHP tests
make test-php

# Specific package
cd packages/analyzers/routes && ../../../vendor/bin/pest

# With coverage
make test-coverage

# Frontend tests
make test-frontend
```

### Linting

```bash
# Fix PHP style
make format-php

# Check PHP style (no fix)
make lint-php

# PHPStan
make analyze

# Fix frontend style
make format-frontend

# Check frontend style
make lint-frontend
```

## Architecture Rules

Before submitting code, verify:

1. No analyzer imports from another analyzer
2. No framework-specific code outside `packages/laravel/`
3. All PHP classes use constructor injection (no service locator)
4. All DTOs are `readonly class`
5. All classes are `final` unless designed for extension
6. JSON output conforms to `JSON_SCHEMA.md`
7. The pipeline is respected: Source → Scanner → AST → Analyzer → DTO → JSON → UI

## Reporting Issues

Use the GitHub issue templates:
- **Bug:** something broken, include reproduction steps
- **Feature:** a new capability, describe the use case
- **Task:** an implementation task, reference the phase/milestone

## Code of Conduct

Be professional. Be respectful. Focus on the work.
