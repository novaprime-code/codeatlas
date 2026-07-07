# Prompt: Add New Analyzer

Use this prompt template when asking Claude to create a brand new analyzer package.

---

## Template

```
Create the `codeatlas/analyzer-{name}` package.

Follow the established pattern from existing analyzers.

This analyzer should:
- Implement `CodeAtlas\Contracts\AnalyzerInterface`
- Declare its supported `NodeType`s and `EdgeType`s
- Accept `ProjectContext` and return `AnalysisResult`
- Use the parser from core for AST operations
- Return typed DTOs (readonly classes)
- Handle parse errors gracefully (log, skip, continue)

Extract:
- {list what this analyzer should extract}

Generate:
- Nodes of type: {node types}
- Edges of type: {edge types}

JSON output must conform to the `{name}` section of `.claude/JSON_SCHEMA.md`.

File structure:
packages/analyzers/{name}/
├── composer.json
├── src/
│   ├── {Name}Analyzer.php
│   ├── DTOs/
│   │   └── {Name}Data.php
│   ├── Extractors/
│   │   └── {Specific}Extractor.php
│   └── Exceptions/
│       └── {Name}AnalyzerException.php
├── tests/
│   ├── Unit/
│   ├── Integration/
│   ├── Fixtures/
│   └── Pest.php
├── benchmarks/
└── README.md

Do NOT:
- Import from any other analyzer
- Add framework-specific code
- Parse PHP with regex
- Skip tests
- Modify existing packages
```
