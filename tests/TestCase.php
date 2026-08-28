<?php

declare(strict_types=1);

namespace Tests;

use Simtabi\Laranail\AuthKit\Social\Providers\SocialServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Simtabi\Laranail\AuthKit\Providers\AuthKitServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /** @return array<int, class-string> */
    protected function getPackageProviders($app): array
    {
        return [
            \Laravel\Fortify\FortifyServiceProvider::class,
            AuthKitServiceProvider::class,
            SocialServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
