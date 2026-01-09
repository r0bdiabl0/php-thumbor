<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor;

use Stringable;

/**
 * Represents a complete Thumbor URL with all transformations applied.
 *
 * This class handles the final URL generation including HMAC signing
 * for secure Thumbor URLs.
 *
 * @see https://github.com/thumbor/thumbor
 */
final class ThumborUrl implements Stringable
{
    /**
     * @param array<int, string> $commands
     */
    public function __construct(
        private readonly string $server,
        private readonly ?string $secret,
        private readonly string $original,
        private readonly array $commands,
    ) {}

    /**
     * Generate the complete Thumbor URL.
     */
    public function build(): string
    {
        $imgPath = count($this->commands) > 0
            ? implode('/', $this->commands) . '/' . $this->original
            : $this->original;

        $signature = $this->secret !== null && $this->secret !== ''
            ? self::sign($imgPath, $this->secret)
            : 'unsafe';

        $server = rtrim($this->server, '/');

        return sprintf('%s/%s/%s', $server, $signature, $imgPath);
    }

    /**
     * Sign a URL path using HMAC-SHA1.
     *
     * @see https://github.com/thumbor/thumbor/wiki/Libraries
     */
    public static function sign(string $path, string $secret): string
    {
        $signature = hash_hmac('sha1', $path, $secret, true);

        return strtr(base64_encode($signature), '/+', '_-');
    }

    public function __toString(): string
    {
        return $this->build();
    }
}
