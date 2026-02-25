<?php

declare(strict_types=1);

namespace Tests;

use JwksDecoder\Providers\JwksServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    #[\Override]
    protected function getPackageProviders($app)
    {
        return [
            JwksServiceProvider::class,
        ];
    }
}
