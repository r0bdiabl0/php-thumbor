<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor;

/**
 * Factory for creating Thumbor URL builders.
 *
 * This is the main entry point for using this library.
 * It creates UrlBuilder instances configured with your server and secret.
 *
 * Example usage (standalone, no framework required):
 *
 *     // Create factory once (e.g., in bootstrap)
 *     $thumbor = new Thumbor('https://thumbor.example.com', 'my-secret-key');
 *
 *     // Use throughout your application
 *     $url = $thumbor->url('https://example.com/image.jpg')
 *         ->fitIn(640, 480)
 *         ->addFilter('quality', 80);
 *
 *     echo $url; // Outputs the signed Thumbor URL
 *
 * For Laravel applications, use the Thumbor facade instead, which provides
 * automatic configuration from your config/thumbor.php file.
 *
 * @see \R0bdiabl0\Thumbor\Laravel\Facades\Thumbor
 */
final class Thumbor
{
    public function __construct(
        private readonly string $server,
        private readonly ?string $secret = null,
    ) {}

    /**
     * Create a factory from server and secret.
     *
     * This static method provides a consistent API with the original
     * 99designs/phumbor library for easier migration.
     */
    public static function construct(string $server, ?string $secret = null): self
    {
        return new self($server, $secret);
    }

    /**
     * Create a URL builder for the given image URL.
     */
    public function url(string $imageUrl): UrlBuilder
    {
        return new UrlBuilder($this->server, $this->secret, $imageUrl);
    }

    /**
     * Get the configured server URL.
     */
    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * Check if a secret key is configured.
     */
    public function hasSecret(): bool
    {
        return $this->secret !== null && $this->secret !== '';
    }
}
