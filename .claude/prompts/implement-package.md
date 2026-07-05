# Prompt: Implement Package

Use this prompt template when asking Claude to implement a specific package.

---

## Template

```
Implement `{package_name}`.

Context:
- Read `.claude/CLAUDE.md` for project rules
- Read `.claude/ARCHITECTURE.md` for architecture
- Read `.claude/CODING_STANDARDS.md` for code style
- Read `.claude/JSON_SCHEMA.md` for output format

Requirements:
- {specific requirements for this package}
- Follow the AnalyzerInterface contract from `packages/contracts/`
- Return DTOs, never raw AST
- Handle errors gracefully (log + skip + continue)

Tests:
- Write Pest tests in `tests/Unit/` and `tests/Integration/`
- Use fixtures in `tests/Fixtures/`
- Target 90%+ coverage

Do NOT:
- Modify any other package
- Add framework-specific code
- Use regex for PHP parsing
- Use `mixed` or `any` types
- Skip tests or documentation
```

---

## Example: Route Analyzer

```
Implement `codeatlas/analyzer-routes`.

Context:
- Read `.claude/CLAUDE.md` for project rules
- Read `.claude/ARCHITECTURE.md` for architecture  
- Read `.claude/CODING_STANDARDS.md` for code style
- Read `.claude/JSON_SCHEMA.md` for the routes output format

Requirements:
- Implement AnalyzerInterface
- Parse routes/web.php, routes/api.php, routes/channels.php, routes/console.php
- Extract: URI, methods, name, controller, action, middleware, prefix, domain, where constraints
- Handle: closure routes, controller routes, resource routes, API resource routes, route groups
- Generate Route nodes and Route→Controller edges
- Output JSON conforming to JSON_SCHEMA.md "Routes Result" section

Tests:
- Create fixture Laravel route files covering all route types
- Test each extraction independently
- Test error handling (malformed files)
- Benchmark: measure files/second

Do NOT:
- Modify packages/contracts/ or packages/core/
- Import from other analyzers
- Use regex to parse PHP
- Execute route files
```
