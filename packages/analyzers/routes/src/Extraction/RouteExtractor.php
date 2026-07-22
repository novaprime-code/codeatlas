<?php

declare(strict_types=1);

namespace CodeAtlas\Analyzers\Routes\Extraction;

use CodeAtlas\Analyzers\Routes\DTOs\GroupContext;
use CodeAtlas\Analyzers\Routes\DTOs\RouteData;
use CodeAtlas\Contracts\ParsedFileInterface;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Stmt\Expression;

/**
 * Extracts every route from a parsed route file.
 *
 * The extractor performs a manual, context-aware descent over the file's
 * top-level statements. It maintains an explicit GroupContext stack so
 * that nested Route::group() closures correctly inherit prefixes,
 * middleware, name prefixes, and domains from their ancestors.
 *
 * It handles two group styles:
 *   - Fluent:  Route::prefix('api')->middleware('auth')->group(fn () => ...)
 *   - Array:   Route::group(['prefix' => 'api', ...], fn () => ...)
 *
 * And these terminal actions: get/post/put/patch/delete/options/any,
 * match(), resource(), apiResource().
 *
 * Every parse decision is AST-based — no regex, no code execution.
 */
final class RouteExtractor
{
    private const HTTP_VERBS = ['get', 'post', 'put', 'patch', 'delete', 'options'];

    private const RESOURCE_METHODS = [
        'index', 'create', 'store', 'show', 'edit', 'update', 'destroy',
    ];

    private const API_RESOURCE_METHODS = [
        'index', 'store', 'show', 'update', 'destroy',
    ];

    private ChainUnwinder $unwinder;

    private ValueResolver $values;

    private ActionResolver $actions;

    public function __construct(private readonly ParsedFileInterface $file)
    {
        $this->unwinder = new ChainUnwinder();
        $this->values = new ValueResolver($file);
        $this->actions = new ActionResolver($file, $this->values);
    }

    /**
     * @param list<Node> $statements
     *
     * @return list<RouteData>
     */
    public function extract(array $statements): array
    {
        return $this->walk($statements, GroupContext::root());
    }

    /**
     * @param list<Node> $statements
     *
     * @return list<RouteData>
     */
    private function walk(array $statements, GroupContext $context): array
    {
        $routes = [];

        foreach ($statements as $stmt) {
            if ($stmt instanceof Node\Stmt\Namespace_) {
                $routes = [...$routes, ...$this->walk($stmt->stmts, $context)];

                continue;
            }

            if (!$stmt instanceof Expression) {
                continue;
            }

            $routes = [...$routes, ...$this->handleExpression($stmt->expr, $context)];
        }

        return $routes;
    }

    /**
     * @return list<RouteData>
     */
    private function handleExpression(Expr $expr, GroupContext $context): array
    {
        $operations = $this->unwinder->unwind($expr);

        if ($operations === null) {
            return [];
        }

        $groupOp = $this->findOperation($operations, 'group');

        if ($groupOp !== null) {
            return $this->handleGroup($operations, $context, $expr);
        }

        return $this->handleRoute($operations, $context, $expr);
    }

    /**
     * @param list<array{name: string, args: list<Arg>}> $operations
     *
     * @return list<RouteData>
     */
    private function handleGroup(array $operations, GroupContext $context, Expr $expr): array
    {
        $prefix = '';
        $middleware = [];
        $namePrefix = '';
        $domain = null;
        $closure = null;

        foreach ($operations as $op) {
            switch ($op['name']) {
                case 'prefix':
                    $prefix = $this->firstString($op['args']) ?? '';
                    break;
                case 'middleware':
                    $middleware = [...$middleware, ...$this->firstStringList($op['args'])];
                    break;
                case 'name':
                    $namePrefix = $this->firstString($op['args']) ?? '';
                    break;
                case 'domain':
                    $domain = $this->firstString($op['args']);
                    break;
                case 'group':
                    $closure = $this->extractGroupClosure($op['args']);
                    $arrayConfig = $this->extractGroupArrayConfig($op['args']);
                    if ($arrayConfig !== null) {
                        $prefix = $arrayConfig['prefix'] ?? $prefix;
                        $middleware = [...$middleware, ...($arrayConfig['middleware'] ?? [])];
                        $namePrefix = $arrayConfig['as'] ?? $namePrefix;
                        $domain = $arrayConfig['domain'] ?? $domain;
                    }
                    break;
            }
        }

        if ($closure === null) {
            return [];
        }

        $childContext = $context->merge(
            prefix: $prefix,
            middleware: $middleware,
            namePrefix: $namePrefix,
            domain: $domain,
        );

        return $this->walk($closure, $childContext);
    }

    /**
     * @param list<array{name: string, args: list<Arg>}> $operations
     *
     * @return list<RouteData>
     */
    private function handleRoute(array $operations, GroupContext $context, Expr $expr): array
    {
        $primary = $operations[0];
        $name = strtolower($primary['name']);

        if (in_array($name, self::HTTP_VERBS, true)) {
            return $this->buildVerbRoute([strtoupper($name)], $primary['args'], $operations, $context, $expr);
        }

        if ($name === 'any') {
            $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

            return $this->buildVerbRoute($methods, $primary['args'], $operations, $context, $expr);
        }

        if ($name === 'match') {
            return $this->buildMatchRoute($primary['args'], $operations, $context, $expr);
        }

        if ($name === 'resource') {
            return $this->buildResourceRoutes($primary['args'], $context, self::RESOURCE_METHODS, $expr);
        }

        if ($name === 'apiresource') {
            return $this->buildResourceRoutes($primary['args'], $context, self::API_RESOURCE_METHODS, $expr);
        }

        return [];
    }

    /**
     * @param list<string> $methods
     * @param list<Arg> $primaryArgs
     * @param list<array{name: string, args: list<Arg>}> $operations
     *
     * @return list<RouteData>
     */
    private function buildVerbRoute(
        array $methods,
        array $primaryArgs,
        array $operations,
        GroupContext $context,
        Expr $expr,
    ): array {
        $uri = isset($primaryArgs[0]) ? $this->values->stringArg($primaryArgs[0]) : null;

        if ($uri === null) {
            return [];
        }

        $action = $this->actions->resolve($primaryArgs[1] ?? null);
        $modifiers = $this->collectModifiers($operations, $context);

        return [new RouteData(
            uri: $context->applyUri($uri),
            methods: $methods,
            name: $context->applyName($modifiers['name']),
            controller: $action->controller,
            action: $action->action,
            isClosure: $action->isClosure,
            middleware: [...$context->middleware, ...$modifiers['middleware']],
            prefix: $context->prefix === '' ? null : $context->prefix,
            domain: $modifiers['domain'] ?? $context->domain,
            where: $modifiers['where'],
            parameters: $this->extractParameters($context->applyUri($uri)),
            line: $expr->getStartLine(),
        )];
    }

    /**
     * @param list<Arg> $primaryArgs
     * @param list<array{name: string, args: list<Arg>}> $operations
     *
     * @return list<RouteData>
     */
    private function buildMatchRoute(
        array $primaryArgs,
        array $operations,
        GroupContext $context,
        Expr $expr,
    ): array {
        if (!isset($primaryArgs[0], $primaryArgs[1])) {
            return [];
        }

        $methods = array_map(strtoupper(...), $this->values->stringListArg($primaryArgs[0]));
        $uri = $this->values->stringArg($primaryArgs[1]);

        if ($methods === [] || $uri === null) {
            return [];
        }

        $action = $this->actions->resolve($primaryArgs[2] ?? null);
        $modifiers = $this->collectModifiers($operations, $context);

        return [new RouteData(
            uri: $context->applyUri($uri),
            methods: array_values($methods),
            name: $context->applyName($modifiers['name']),
            controller: $action->controller,
            action: $action->action,
            isClosure: $action->isClosure,
            middleware: [...$context->middleware, ...$modifiers['middleware']],
            prefix: $context->prefix === '' ? null : $context->prefix,
            domain: $modifiers['domain'] ?? $context->domain,
            where: $modifiers['where'],
            parameters: $this->extractParameters($context->applyUri($uri)),
            line: $expr->getStartLine(),
        )];
    }

    /**
     * @param list<Arg> $primaryArgs
     * @param list<string> $resourceMethods
     *
     * @return list<RouteData>
     */
    private function buildResourceRoutes(
        array $primaryArgs,
        GroupContext $context,
        array $resourceMethods,
        Expr $expr,
    ): array {
        $name = isset($primaryArgs[0]) ? $this->values->stringArg($primaryArgs[0]) : null;
        $controller = isset($primaryArgs[1]) ? $this->values->classConstFqcn($primaryArgs[1]->value) : null;

        if ($name === null) {
            return [];
        }

        $verbMap = [
            'index' => ['GET', ''],
            'create' => ['GET', '/create'],
            'store' => ['POST', ''],
            'show' => ['GET', '/{' . $this->singularize($name) . '}'],
            'edit' => ['GET', '/{' . $this->singularize($name) . '}/edit'],
            'update' => ['PUT', '/{' . $this->singularize($name) . '}'],
            'destroy' => ['DELETE', '/{' . $this->singularize($name) . '}'],
        ];

        $routes = [];

        foreach ($resourceMethods as $method) {
            [$verb, $suffix] = $verbMap[$method];
            $uri = $context->applyUri($name . $suffix);

            $routes[] = new RouteData(
                uri: $uri,
                methods: [$verb],
                name: $context->applyName($name . '.' . $method),
                controller: $controller,
                action: $method,
                isClosure: false,
                middleware: $context->middleware,
                prefix: $context->prefix === '' ? null : $context->prefix,
                domain: $context->domain,
                where: [],
                parameters: $this->extractParameters($uri),
                line: $expr->getStartLine(),
            );
        }

        return $routes;
    }

    /**
     * @param list<array{name: string, args: list<Arg>}> $operations
     *
     * @return array{name: ?string, middleware: list<string>, domain: ?string, where: array<string, string>}
     */
    private function collectModifiers(array $operations, GroupContext $context): array
    {
        $name = null;
        $middleware = [];
        $domain = null;
        $where = [];

        foreach ($operations as $i => $op) {
            if ($i === 0) {
                continue;
            }

            switch (strtolower($op['name'])) {
                case 'name':
                    $name = $this->firstString($op['args']);
                    break;
                case 'middleware':
                    $middleware = [...$middleware, ...$this->firstStringList($op['args'])];
                    break;
                case 'domain':
                    $domain = $this->firstString($op['args']);
                    break;
                case 'where':
                    $where = [...$where, ...$this->extractWhere($op['args'])];
                    break;
                case 'wherenumber':
                    $where = [...$where, ...$this->whereShortcut($op['args'], '[0-9]+')];
                    break;
                case 'wherealpha':
                    $where = [...$where, ...$this->whereShortcut($op['args'], '[a-zA-Z]+')];
                    break;
                case 'wherealphanumeric':
                    $where = [...$where, ...$this->whereShortcut($op['args'], '[a-zA-Z0-9]+')];
                    break;
                case 'whereuuid':
                    $where = [...$where, ...$this->whereShortcut($op['args'], '[\da-fA-F-]+')];
                    break;
            }
        }

        return ['name' => $name, 'middleware' => $middleware, 'domain' => $domain, 'where' => $where];
    }

    /**
     * @param list<Arg> $args
     *
     * @return array<string, string>
     */
    private function extractWhere(array $args): array
    {
        if (isset($args[0]) && $args[0]->value instanceof \PhpParser\Node\Expr\Array_) {
            return $this->values->stringMapArg($args[0]);
        }

        if (isset($args[0], $args[1])) {
            $key = $this->values->stringArg($args[0]);
            $pattern = $this->values->stringArg($args[1]);
            if ($key !== null && $pattern !== null) {
                return [$key => $pattern];
            }
        }

        return [];
    }

    /**
     * @param list<Arg> $args
     *
     * @return array<string, string>
     */
    private function whereShortcut(array $args, string $pattern): array
    {
        $key = isset($args[0]) ? $this->values->stringArg($args[0]) : null;

        return $key === null ? [] : [$key => $pattern];
    }

    /**
     * @param list<Arg> $args
     *
     * @return list<Node\Stmt>|null
     */
    private function extractGroupClosure(array $args): ?array
    {
        foreach ($args as $arg) {
            if ($arg->value instanceof Closure) {
                return $arg->value->stmts;
            }
            if ($arg->value instanceof ArrowFunction) {
                return [new Expression($arg->value->expr)];
            }
        }

        return null;
    }

    /**
     * @param list<Arg> $args
     *
     * @return array{prefix?: string, middleware?: list<string>, as?: string, domain?: string}|null
     */
    private function extractGroupArrayConfig(array $args): ?array
    {
        foreach ($args as $arg) {
            if (!$arg->value instanceof \PhpParser\Node\Expr\Array_) {
                continue;
            }

            $config = [];

            foreach ($arg->value->items as $item) {
                if (!$item instanceof \PhpParser\Node\Expr\ArrayItem || $item->key === null) {
                    continue;
                }

                $key = $this->values->stringExpr($item->key);

                if ($key === 'prefix') {
                    $val = $this->values->stringExpr($item->value);
                    if ($val !== null) {
                        $config['prefix'] = $val;
                    }
                } elseif ($key === 'as') {
                    $val = $this->values->stringExpr($item->value);
                    if ($val !== null) {
                        $config['as'] = $val;
                    }
                } elseif ($key === 'domain') {
                    $val = $this->values->stringExpr($item->value);
                    if ($val !== null) {
                        $config['domain'] = $val;
                    }
                } elseif ($key === 'middleware') {
                    if ($item->value instanceof \PhpParser\Node\Scalar\String_) {
                        $config['middleware'] = [$item->value->value];
                    } elseif ($item->value instanceof \PhpParser\Node\Expr\Array_) {
                        $config['middleware'] = $this->values->stringListFromArray($item->value);
                    }
                }
            }

            return $config;
        }

        return null;
    }

    /**
     * @param list<array{name: string, args: list<Arg>}> $operations
     *
     * @return array{name: string, args: list<Arg>}|null
     */
    private function findOperation(array $operations, string $name): ?array
    {
        foreach ($operations as $op) {
            if (strtolower($op['name']) === $name) {
                return $op;
            }
        }

        return null;
    }

    /**
     * @param list<Arg> $args
     */
    private function firstString(array $args): ?string
    {
        return isset($args[0]) ? $this->values->stringArg($args[0]) : null;
    }

    /**
     * @param list<Arg> $args
     *
     * @return list<string>
     */
    private function firstStringList(array $args): array
    {
        return isset($args[0]) ? $this->values->stringListArg($args[0]) : [];
    }

    /**
     * @return list<string>
     */
    private function extractParameters(string $uri): array
    {
        preg_match_all('/\{(\w+)\??\}/', $uri, $matches);

        /** @var list<string> $params */
        $params = $matches[1];

        return $params;
    }

    private function singularize(string $name): string
    {
        $base = str_contains($name, '/') ? substr(strrchr($name, '/') ?: $name, 1) : $name;

        if (str_ends_with($base, 'ies')) {
            return substr($base, 0, -3) . 'y';
        }

        if (str_ends_with($base, 's')) {
            return substr($base, 0, -1);
        }

        return $base;
    }
}
