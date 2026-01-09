<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor;

use BadMethodCallException;
use Stringable;

/**
 * Fluent builder for constructing Thumbor URLs.
 *
 * Provides a chainable API for applying image transformations.
 * Supports all Thumbor 7.7.7 features including modern formats (AVIF, WebP, HEIC).
 *
 * Example usage:
 *
 *     $thumbor = new Thumbor('https://thumbor.example.com', 'my-secret-key');
 *     $url = $thumbor->url('https://example.com/image.jpg')
 *         ->fitIn(640, 480)
 *         ->smartCrop(true)
 *         ->webp()
 *         ->quality(80);
 *
 *     echo $url; // Outputs the signed Thumbor URL
 *
 * @method $this trim(?string $colourSource = null, ?int $tolerance = null) Trim surrounding space
 * @method $this crop(int $topLeftX, int $topLeftY, int $bottomRightX, int $bottomRightY) Set crop coordinates
 * @method $this fullFitIn(int $width, int $height) Fit by smallest side
 * @method $this fitIn(int $width, int $height) Fit in box
 * @method $this adaptiveFitIn(int $width, int $height) Adaptive fit-in for better definition
 * @method $this resize(int|string $width, int|string $height) Resize to dimensions
 * @method $this flipHorizontal(bool $flip = true) Flip image horizontally
 * @method $this flipVertical(bool $flip = true) Flip image vertically
 * @method $this halign(string $halign) Horizontal alignment (left/center/right)
 * @method $this valign(string $valign) Vertical alignment (top/middle/bottom)
 * @method $this smartCrop(bool $smartCrop) Enable smart cropping with face detection
 * @method $this addFilter(string $filter, string|int|float|bool ...$args) Add a custom filter
 * @method $this metadataOnly(bool $metadataOnly) Return JSON metadata instead of image
 * @method $this quality(int $quality) Set JPEG quality (1-100)
 * @method $this format(string $format) Convert to format (webp/jpeg/png/gif/avif/heic). AVIF/HEIC require Thumbor 7+
 * @method $this webp() Convert to WebP format
 * @method $this avif() Convert to AVIF format [Thumbor 7+]
 * @method $this blur(int $radius, ?int $sigma = null) Apply blur effect
 * @method $this brightness(int $amount) Adjust brightness (-100 to 100)
 * @method $this contrast(int $amount) Adjust contrast (-100 to 100)
 * @method $this grayscale() Convert to grayscale
 * @method $this rotate(int $angle) Rotate image (0, 90, 180, 270)
 * @method $this sharpen(float $amount, float $radius, bool $luminanceOnly = false) Sharpen image
 * @method $this noise(int $amount) Add noise (0-100)
 * @method $this watermark(string $imageUrl, int $x = 0, int $y = 0, int $alpha = 0) Add watermark
 * @method $this fill(string $color) Fill empty space (hex/auto/blur/transparent)
 * @method $this roundCorners(int $radius, ?int $red = null, ?int $green = null, ?int $blue = null) Round corners
 * @method $this stripExif() Remove EXIF metadata
 * @method $this stripIcc() Remove ICC color profile
 * @method $this noUpscale() Prevent upscaling
 * @method $this saturation(float $amount) Adjust saturation (0.0-2.0) [Thumbor 7+]
 * @method $this rgb(int $red, int $green, int $blue) Adjust RGB channels (-100 to 100 each)
 * @method $this maxBytes(int $bytes) Limit maximum file size [Thumbor 7+]
 * @method $this equalize() Apply histogram equalization [Thumbor 7+]
 * @method $this convolution(array $matrix, int $columns, bool $normalize = false) Apply convolution matrix
 *
 * @see https://thumbor.readthedocs.io/en/latest/usage.html
 * @see https://thumbor.readthedocs.io/en/latest/filters.html
 */
final class UrlBuilder implements Stringable
{
    private CommandSet $commands;

    public function __construct(
        private readonly string $server,
        private readonly ?string $secret,
        private readonly string $original,
    ) {
        $this->commands = new CommandSet();
    }

    /**
     * Clone the command set when cloning the builder.
     */
    public function __clone(): void
    {
        $this->commands = clone $this->commands;
    }

    /**
     * Proxy method calls to the CommandSet for a fluent interface.
     *
     * @param array<int, mixed> $args
     */
    public function __call(string $method, array $args): self
    {
        if (!method_exists($this->commands, $method)) {
            throw new BadMethodCallException(
                sprintf('Method "%s" does not exist on %s', $method, CommandSet::class)
            );
        }

        $this->commands->{$method}(...$args);

        return $this;
    }

    /**
     * Build and return the ThumborUrl object.
     */
    public function build(): ThumborUrl
    {
        return new ThumborUrl(
            $this->server,
            $this->secret,
            $this->original,
            $this->commands->toArray()
        );
    }

    /**
     * Get the URL as a string.
     */
    public function __toString(): string
    {
        return (string) $this->build();
    }
}
