<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Parser;

use CodeAtlas\Contracts\ParsedFileInterface;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use PhpParser\NodeTraverser;

/**
 * Structured result of parsing a single PHP file.
 *
 * Wraps the raw AST from nikic/php-parser and pre-computes the metadata
 * analyzers most often need: namespace, use statements, and class-like
 * declarations. The raw AST remains accessible for analyzer-specific
 * traversal.
 *
 * The class is intentionally immutable — every extraction happens once
 * at construction and is cached in properties.
 *
 * @phpstan-type UseMap array<string, string>
 */
final class ParsedFile implements ParsedFileInterface
{
    /** @var list<Node> */
    private readonly array $ast;

    private readonly ?string $namespace;

    /** @var UseMap */
    private readonly array $useStatements;

    /** @var list<string> */
    private readonly array $classNames;

    /**
     * @param list<Node> $ast
     */
    public function __construct(
        private readonly string $path,
        array $ast,
    ) {
        $this->ast = $ast;
        $this->namespace = $this->extractNamespace();
        $this->useStatements = $this->extractUseStatements();
        $this->classNames = $this->extractClassNames();
    }

    public function path(): string
    {
        return $this->path;
    }

    public function namespace(): ?string
    {
        return $this->namespace;
    }

    public function useStatements(): array
    {
        return $this->useStatements;
    }

    public function classNames(): array
    {
        return $this->classNames;
    }

    /**
     * @return list<Node>
     */
    public function ast(): array
    {
        return $this->ast;
    }

    /**
     * Traverse the AST with a visitor, yielding nodes of a given class.
     *
     * @template T of Node
     *
     * @param class-string<T> $nodeClass
     *
     * @return list<T>
     */
    public function findNodes(string $nodeClass): array
    {
        $collector = new NodeCollector($nodeClass);
        $traverser = new NodeTraverser();
        $traverser->addVisitor($collector);
        $traverser->traverse($this->ast);

        /** @var list<T> $found */
        $found = $collector->found;

        return $found;
    }

    /**
     * Resolve an unqualified or aliased class name against the file's use statements.
     *
     * "UserService" + use App\Services\UserService => "App\Services\UserService"
     * "User\Foo" + use App\Models as User          => "App\Models\Foo"
     * "\Absolute\Ref"                              => "Absolute\Ref"
     * "Bare" (no import, no namespace)             => "Bare"
     * "Bare" (no import, namespace App)            => "App\Bare"
     */
    public function resolveClassName(string $name): string
    {
        if (str_starts_with($name, '\\')) {
            return ltrim($name, '\\');
        }

        [$head, $tail] = $this->splitAtFirstSeparator($name);

        if (isset($this->useStatements[$head])) {
            return $tail === null
                ? $this->useStatements[$head]
                : $this->useStatements[$head] . '\\' . $tail;
        }

        return $this->namespace === null ? $name : $this->namespace . '\\' . $name;
    }

    private function extractNamespace(): ?string
    {
        foreach ($this->ast as $node) {
            if ($node instanceof Namespace_ && $node->name !== null) {
                return $node->name->toString();
            }
        }

        return null;
    }

    /**
     * @return UseMap
     */
    private function extractUseStatements(): array
    {
        $map = [];

        foreach ($this->collectUses() as $use) {
            if ($use instanceof GroupUse) {
                $prefix = $use->prefix->toString();

                foreach ($use->uses as $item) {
                    $tail = $item->name->toString();
                    $fqcn = $prefix . '\\' . $tail;
                    $alias = $item->alias->name ?? $this->basename($fqcn);
                    $map[$alias] = $fqcn;
                }

                continue;
            }

            foreach ($use->uses as $item) {
                if (!$item instanceof UseItem) {
                    continue;
                }

                $fqcn = $item->name->toString();
                $alias = $item->alias->name ?? $this->basename($fqcn);
                $map[$alias] = $fqcn;
            }
        }

        return $map;
    }

    /**
     * @return list<Use_|GroupUse>
     */
    private function collectUses(): array
    {
        $uses = [];

        foreach ($this->ast as $node) {
            if ($node instanceof Use_ || $node instanceof GroupUse) {
                $uses[] = $node;

                continue;
            }

            if ($node instanceof Namespace_) {
                foreach ($node->stmts as $inner) {
                    if ($inner instanceof Use_ || $inner instanceof GroupUse) {
                        $uses[] = $inner;
                    }
                }
            }
        }

        return $uses;
    }

    /**
     * @return list<string>
     */
    private function extractClassNames(): array
    {
        $names = [];

        foreach ($this->findNodes(ClassLike::class) as $node) {
            if (!$node instanceof Class_ && !$node instanceof Interface_ && !$node instanceof Trait_ && !$node instanceof Enum_) {
                continue;
            }

            if ($node->name === null) {
                continue;
            }

            $names[] = $this->namespace === null
                ? $node->name->toString()
                : $this->namespace . '\\' . $node->name->toString();
        }

        return $names;
    }

    private function basename(string $fqcn): string
    {
        $pos = strrpos($fqcn, '\\');

        return $pos === false ? $fqcn : substr($fqcn, $pos + 1);
    }

    /**
     * @return array{0: string, 1: string|null}
     */
    private function splitAtFirstSeparator(string $name): array
    {
        $pos = strpos($name, '\\');

        if ($pos === false) {
            return [$name, null];
        }

        return [substr($name, 0, $pos), substr($name, $pos + 1)];
    }
}
