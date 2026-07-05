# Prompt: Write Tests

Use this prompt template when asking Claude to add tests to an existing package.

---

## Template

```
Write tests for `{package_name}`.

Test the following:
- {specific functionality to test}

Rules:
- Use Pest (PHP) or Vitest (TypeScript)
- Use `describe()` + `it()` with descriptive names
- Use `expect()` API, not `assert*()`
- Place unit tests in `tests/Unit/`
- Place integration tests in `tests/Integration/`
- Use fixtures in `tests/Fixtures/` (create them if needed)
- Test happy path, edge cases, and error handling
- Target 90%+ line coverage

Do NOT:
- Modify source code (only add tests)
- Create temporary files (use fixtures)
- Test implementation details (test behavior)
- Write trivial getter/setter tests
```

---

## Example: Scanner Tests

```
Write tests for `codeatlas/scanner`.

Test the following:
- Scanning a valid Laravel project discovers all expected directories
- Missing directories are handled gracefully (no crash)
- Custom path configuration is respected
- Exclusion patterns work (glob-based)
- ProjectContext contains correct file counts and types
- Scanning a non-Laravel project returns appropriate error/warning
- Scanning an empty directory returns empty ProjectContext
- Large directory scanning completes within performance bounds

Rules:
- Create a fixture directory at `tests/Fixtures/laravel-app/` with minimal Laravel structure
- Create a fixture at `tests/Fixtures/empty-project/`
- Create a fixture at `tests/Fixtures/non-laravel/`
- Use Pest
- Test each scanner method independently in unit tests
- Test full scan flow in integration tests
```
