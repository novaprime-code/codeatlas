<?php

declare(strict_types=1);

namespace CodeAtlas\Laravel\Tests;

use CodeAtlas\Laravel\CodeAtlasServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;

abstract class TestbenchCase extends TestCase
{
    /**
     * @param Application $app
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [CodeAtlasServiceProvider::class];
    }
}
