# codeatlas/analyzer-routes

Route analyzer for CodeAtlas. Extracts every route from a Laravel project via AST — URI, methods, name, controller, action, middleware, prefix, domain, parameters, and constraints.

Handles: basic routes, controller routes, closure routes, invokable controllers, resource routes, API resources, nested groups.

Generates `Route` nodes plus `Route→Controller` and `Route→Middleware` edges.

Part of the [CodeAtlas](https://github.com/novaprime-code/codeatlas) monorepo. MIT © Snova Labs.
