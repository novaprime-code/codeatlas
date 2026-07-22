<?php

declare(strict_types=1);

pest()->extend(PHPUnit\Framework\TestCase::class)->in('Unit');
pest()->extend(CodeAtlas\Laravel\Tests\TestbenchCase::class)->in('Integration');
