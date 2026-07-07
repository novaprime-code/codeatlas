# codeatlas/core

The CodeAtlas engine. Framework-agnostic by design — no Laravel, no Symfony framework code.

## Components

| Component      | Purpose                                          |
| -------------- | ------------------------------------------------ |
| Container      | Minimal DI with reflection-based auto-resolution |
| Config         | Array/file loading with dot-notation access      |
| PluginLoader   | Auto-discovers analyzers and exporters           |
| EventBus       | Sync in-process pipeline events                  |
| PipelineRunner | Orchestrates scan → analyze → export             |
| PhpParser      | Typed wrapper around nikic/php-parser v5         |

Part of the [CodeAtlas](https://github.com/novaprime-code/codeatlas) monorepo. MIT © Snova Labs.
