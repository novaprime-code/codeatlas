<?php

declare(strict_types=1);

namespace CodeAtlas\Analyzers\Routes\Extraction;

use CodeAtlas\Core\Parser\ParsedFile;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Scalar\String_;

/**
 * Resolves a route's action argument into (controller, method, isClosure).
 *
 * Laravel accepts several action shapes:
 *   [UserController::class, 'index']   → controller + method
 *   UserController::class              → invokable, method = "__invoke"
 *   'UserController@index'             → legacy string, split on @
 *   fn () => ... / function () { }      → closure, no controller
 *
 * Anything unresolvable (a variable, a dynamic expression) yields a null
 * controller without marking the route a closure — the route still exists,
 * we just cannot statically name its handler.
 *
 * Typed against concrete ParsedFile for resolveClassName(); see the note
 * on ValueResolver for the rationale.
 */
final class ActionResolver
{
    public function __construct(
        private readonly ParsedFile $file,
        private readonly ValueResolver $values,
    ) {}

    public function resolve(?Arg $arg): ActionResult
    {
        if ($arg === null) {
            return new ActionResult(controller: null, action: null, isClosure: false);
        }

        $value = $arg->value;

        if ($value instanceof Closure || $value instanceof ArrowFunction) {
            return new ActionResult(controller: null, action: null, isClosure: true);
        }

        if ($value instanceof Array_) {
            return $this->resolveArrayAction($value);
        }

        $fqcn = $this->values->classConstFqcn($value);
        if ($fqcn !== null) {
            return new ActionResult(controller: $fqcn, action: '__invoke', isClosure: false);
        }

        if ($value instanceof String_) {
            return $this->resolveStringAction($value->value);
        }

        return new ActionResult(controller: null, action: null, isClosure: false);
    }

    private function resolveArrayAction(Array_ $array): ActionResult
    {
        $items = array_values(array_filter(
            $array->items,
            static fn($item): bool => $item instanceof ArrayItem,
        ));

        $controller = null;
        $action = null;

        if (isset($items[0]) && $items[0] instanceof ArrayItem) {
            $controller = $this->values->classConstFqcn($items[0]->value);
        }

        if (isset($items[1]) && $items[1] instanceof ArrayItem && $items[1]->value instanceof String_) {
            $action = $items[1]->value->value;
        }

        return new ActionResult(
            controller: $controller,
            action: $action ?? ($controller !== null ? '__invoke' : null),
            isClosure: false,
        );
    }

    private function resolveStringAction(string $action): ActionResult
    {
        if (!str_contains($action, '@')) {
            return new ActionResult(controller: null, action: null, isClosure: false);
        }

        [$class, $method] = explode('@', $action, 2);

        return new ActionResult(
            controller: $this->file->resolveClassName($class),
            action: $method,
            isClosure: false,
        );
    }
}
