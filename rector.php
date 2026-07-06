<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictScalarReturnExprRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/packages/contracts/src',
        __DIR__ . '/packages/core/src',
        __DIR__ . '/packages/scanner/src',
        __DIR__ . '/packages/laravel/src',
        __DIR__ . '/packages/analyzers/routes/src',
        __DIR__ . '/packages/exporters/json/src',
    ])
    ->withPhpSets(php83: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
        strictBooleans: true,
    )
    ->withRules([
        AddOverrideAttributeToOverriddenMethodsRector::class,
        ReturnTypeFromStrictNativeCallRector::class,
        ReturnTypeFromStrictScalarReturnExprRector::class,
    ])
    ->withImportNames(importShortClasses: false);
