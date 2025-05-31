<?php

namespace Eugenefvdm\NotificationSubscriptions\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Register our factory namespace
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Database\\Factories\\' . class_basename($modelName) . 'Factory';
        });

        // Configure view paths
        $app['config']->set('view.paths', [
            __DIR__ . '/../resources/views',
            __DIR__ . '/resources/views',
        ]);

        // Register test views namespace
        $app['view']->addNamespace('test', __DIR__ . '/views');
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Eugenefvdm\NotificationSubscriptions\NotificationSubscriptionsServiceProvider::class,
        ];
    }
} 