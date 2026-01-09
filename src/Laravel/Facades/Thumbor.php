<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use R0bdiabl0\Thumbor\Thumbor as ThumborFactory;
use R0bdiabl0\Thumbor\UrlBuilder;

/**
 * Thumbor Facade for Laravel.
 *
 * Provides convenient static access to the Thumbor URL builder.
 *
 * Example usage:
 *
 *     use R0bdiabl0\Thumbor\Laravel\Facades\Thumbor;
 *
 *     $url = Thumbor::url('https://example.com/image.jpg')
 *         ->fitIn(640, 480)
 *         ->addFilter('quality', 80);
 *
 * @method static UrlBuilder url(string $imageUrl) Create a URL builder for the given image URL
 * @method static string getServer() Get the configured server URL
 * @method static bool hasSecret() Check if a secret key is configured
 *
 * @see \R0bdiabl0\Thumbor\Thumbor
 */
class Thumbor extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'thumbor';
    }
}
