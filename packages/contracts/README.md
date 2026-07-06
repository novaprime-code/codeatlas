# codeatlas/contracts

Interfaces, enums, and value objects that define the CodeAtlas plugin API.

**Zero dependencies.** Every other CodeAtlas package implements these contracts.

## Key contracts

| Contract                                             | Purpose                        |
| ---------------------------------------------------- | ------------------------------ |
| `AnalyzerInterface`                                  | Every analyzer implements this |
| `ScannerInterface`                                   | File discovery                 |
| `ExporterInterface`                                  | Output generation              |
| `ParserInterface`                                    | AST parsing abstraction        |
| `NodeInterface` / `EdgeInterface` / `GraphInterface` | Graph primitives               |

## Installation

```bash
composer require codeatlas/contracts
```

Part of the [CodeAtlas](https://github.com/novaprime-code/codeatlas) monorepo. MIT © Snova Labs.
