<?php

declare(strict_types=1);

namespace CodeAtlas\Analyzers\Routes\Extraction;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;

/**
 * Flattens a fluent Route chain into an ordered list of operations.
 *
 * Laravel route definitions are method chains rooted at a static call:
 *
 *   Route::prefix('api')->middleware('auth')->group(fn () => ...)
 *   Route::get('/x', $action)->name('x')->where('id', '[0-9]+')
 *
 * In the AST these are right-leaning MethodCall nodes wrapping a
 * StaticCall. This class walks that structure and returns operations in
 * source order (outermost static call first), so the extractor can reason
 * about them linearly instead of recursing through nested AST nodes.
 *
 * @phpstan-type Operation array{name: string, args: list<\PhpParser\Node\Arg>}
 */
final class ChainUnwinder
{
    /**
     * @return list<array{name: string, args: list<\PhpParser\Node\Arg>}>|null
     *   Null when the expression is not a static-rooted call chain.
     */
    public function unwind(Expr $expr): ?array
    {
        $methodCalls = [];
        $current = $expr;

        while ($current instanceof MethodCall) {
            $methodCalls[] = $current;
            $current = $current->var;
        }

        if (!$current instanceof StaticCall) {
            return null;
        }

        if (!$this->isRouteFacade($current)) {
            return null;
        }

        $operations = [];

        $rootName = $this->identifierName($current->name);
        if ($rootName === null) {
            return null;
        }

        $operations[] = ['name' => $rootName, 'args' => $this->args($current->getArgs())];

        foreach (array_reverse($methodCalls) as $call) {
            $name = $this->identifierName($call->name);
            if ($name === null) {
                continue;
            }
            $operations[] = ['name' => $name, 'args' => $this->args($call->getArgs())];
        }

        return $operations;
    }

    private function isRouteFacade(StaticCall $call): bool
    {
        if (!$call->class instanceof \PhpParser\Node\Name) {
            return false;
        }

        $class = $call->class->toString();

        return $class === 'Route'
            || $class === 'Illuminate\\Support\\Facades\\Route'
            || str_ends_with($class, '\\Route');
    }

    private function identifierName(mixed $name): ?string
    {
        return $name instanceof Identifier ? $name->toString() : null;
    }

    /**
     * @param array<int, \PhpParser\Node\Arg> $args
     *
     * @return list<\PhpParser\Node\Arg>
     */
    private function args(array $args): array
    {
        return array_values($args);
    }
}
