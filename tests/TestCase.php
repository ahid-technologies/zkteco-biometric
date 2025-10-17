<?php

namespace AhidTechnologies\ZKTecoBiometric\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use AhidTechnologies\ZKTecoBiometric\ZKTecoBiometricServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            ZKTecoBiometricServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
