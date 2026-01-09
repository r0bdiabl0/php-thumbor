<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor\Laravel;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use R0bdiabl0\Thumbor\Thumbor;

/**
 * Laravel service provider for Thumbor integration.
 *
 * This provider registers the Thumbor factory in the service container
 * and handles configuration publishing.
 *
 * For Laravel 5.5+, this provider is auto-discovered.
 */
class ThumborServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/thumbor.php',
            'thumbor'
        );

        $this->app->singleton('thumbor', function (Container $app): Thumbor {
            /** @var string $server */
            $server = $app['config']->get('thumbor.server', '');

            /** @var string|null $key */
            $key = $app['config']->get('thumbor.key');

            return new Thumbor($server, $key);
        });

        $this->app->alias('thumbor', Thumbor::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/thumbor.php' => config_path('thumbor.php'),
            ], 'thumbor-config');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return ['thumbor', Thumbor::class];
    }
}
