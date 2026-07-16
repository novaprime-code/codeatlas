<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Parser;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * Internal visitor that collects every AST node of a given class.
 *
 * @internal
 */
final class NodeCollector extends NodeVisitorAbstract
{
    /** @var list<Node> */
    public array $found = [];

    /**
     * @param class-string<Node> $nodeClass
     */
    public function __construct(private readonly string $nodeClass) {}

    public function enterNode(Node $node): ?int
    {
        if ($node instanceof $this->nodeClass) {
            $this->found[] = $node;
        }

        return null;
    }
}
