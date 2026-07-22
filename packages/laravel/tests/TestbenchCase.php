<?php

declare(strict_types=1);

namespace CodeAtlas\Laravel\Tests;

use CodeAtlas\Laravel\CodeAtlasServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class TestbenchCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [CodeAtlasServiceProvider::class];
    }
}
