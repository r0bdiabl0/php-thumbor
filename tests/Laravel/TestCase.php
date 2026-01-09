<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor\Tests\Laravel;

use Orchestra\Testbench\TestCase as BaseTestCase;
use R0bdiabl0\Thumbor\Laravel\Facades\Thumbor;
use R0bdiabl0\Thumbor\Laravel\ThumborServiceProvider;

abstract class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ThumborServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Thumbor' => Thumbor::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('thumbor.server', 'http://thumbor.example.com');
        $app['config']->set('thumbor.key', 'test-secret-key');
    }
}
