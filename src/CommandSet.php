<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor;

/**
 * A set of image manipulation commands for Thumbor 7.x.
 *
 * This class builds the URL path components that define image transformations.
 * Supports all Thumbor 7.7.7 features including modern format support (AVIF, WebP, HEIC).
 *
 * @see https://thumbor.readthedocs.io/en/latest/usage.html
 */
final class CommandSet
{
    private ?string $trim = null;
    private ?string $crop = null;
    private ?string $resize = null;
    private ?string $halign = null;
    private ?string $valign = null;
    private bool $smartCrop = false;
    private bool $metadataOnly = false;
    private bool $flipHorizontal = false;
    private bool $flipVertical = false;

    /** @var array<int, string> */
    private array $filters = [];

    /**
     * Trim surrounding space from the thumbnail.
     *
     * The top-left corner of the image is assumed to contain the background colour.
     * To specify otherwise, pass 'top-left' or 'bottom-right' as the $colourSource.
     *
     * @param string|null $colourSource 'top-left' or 'bottom-right'
     * @param int|null $tolerance Euclidean distance tolerance (0-442 for RGB)
     */
    public function trim(?string $colourSource = null, ?int $tolerance = null): void
    {
        $this->trim = 'trim';
        if ($colourSource !== null) {
            $this->trim .= ':' . $colourSource;
        }
        if ($tolerance !== null) {
            $this->trim .= ':' . $tolerance;
        }
    }

    /**
     * Manually specify crop window coordinates.
     */
    public function crop(int $topLeftX, int $topLeftY, int $bottomRightX, int $bottomRightY): void
    {
        $this->crop = "{$topLeftX}x{$topLeftY}:{$bottomRightX}x{$bottomRightY}";
    }

    /**
     * Resize the image to fit by smallest side in a box of the specified dimensions.
     *
     * Overrides any previous call to fitIn(), fullFitIn(), adaptiveFitIn(), or resize().
     */
    public function fullFitIn(int $width, int $height): void
    {
        $this->resize = "full-fit-in/{$width}x{$height}";
    }

    /**
     * Resize the image to fit in a box of the specified dimensions.
     *
     * Overrides any previous call to fitIn(), fullFitIn(), adaptiveFitIn(), or resize().
     */
    public function fitIn(int $width, int $height): void
    {
        $this->resize = "fit-in/{$width}x{$height}";
    }

    /**
     * Adaptive fit-in: inverts requested dimensions for better definition.
     *
     * If the image dimensions are larger than the requested size, behaves like fit-in.
     * If smaller, the image is not upscaled.
     *
     * Overrides any previous call to fitIn(), fullFitIn(), adaptiveFitIn(), or resize().
     */
    public function adaptiveFitIn(int $width, int $height): void
    {
        $this->resize = "adaptive-fit-in/{$width}x{$height}";
    }

    /**
     * Resize the image to the specified dimensions.
     *
     * Use 0 for proportional resizing. E.g. for a 640x480 image,
     * resize(320, 0) yields a 320x240 thumbnail.
     *
     * Use 'orig' to use an original image dimension. E.g. for a 640x480 image,
     * resize(320, 'orig') yields a 320x480 thumbnail.
     *
     * Overrides any previous call to fitIn(), fullFitIn(), adaptiveFitIn(), or resize().
     */
    public function resize(int|string $width, int|string $height): void
    {
        $this->resize = "{$width}x{$height}";
    }

    /**
     * Flip the image horizontally.
     */
    public function flipHorizontal(bool $flip = true): void
    {
        $this->flipHorizontal = $flip;
    }

    /**
     * Flip the image vertically.
     */
    public function flipVertical(bool $flip = true): void
    {
        $this->flipVertical = $flip;
    }

    /**
     * Specify horizontal alignment used if width is altered due to cropping.
     *
     * @param string $halign 'left', 'center', or 'right'
     */
    public function halign(string $halign): void
    {
        $this->halign = $halign;
    }

    /**
     * Specify vertical alignment used if height is altered due to cropping.
     *
     * @param string $valign 'top', 'middle', or 'bottom'
     */
    public function valign(string $valign): void
    {
        $this->valign = $valign;
    }

    /**
     * Enable smart cropping (overrides halign/valign).
     *
     * Uses face detection and feature detection algorithms to find
     * the most relevant area of the image.
     */
    public function smartCrop(bool $smartCrop): void
    {
        $this->smartCrop = $smartCrop;
    }

    /**
     * Add a filter to the image transformation.
     *
     * @param string $filter The filter name (e.g., 'brightness', 'contrast', 'quality')
     * @param string|int|float|bool ...$args Filter arguments
     *
     * @see https://thumbor.readthedocs.io/en/latest/filters.html
     */
    public function addFilter(string $filter, string|int|float|bool ...$args): void
    {
        $stringArgs = array_map(function ($arg) {
            if (is_bool($arg)) {
                return $arg ? 'true' : 'false';
            }
            return (string) $arg;
        }, $args);

        $this->filters[] = sprintf('%s(%s)', $filter, implode(',', $stringArgs));
    }

    // ==========================================
    // Convenience methods for common filters
    // ==========================================

    /**
     * Set JPEG quality (1-100).
     *
     * @param int $quality Quality level (1-100)
     */
    public function quality(int $quality): void
    {
        $this->addFilter('quality', $quality);
    }

    /**
     * Convert to a specific format.
     *
     * @param string $format One of: 'webp', 'jpeg', 'png', 'gif', 'avif', 'heic'
     */
    public function format(string $format): void
    {
        $this->addFilter('format', $format);
    }

    /**
     * Convert to WebP format.
     */
    public function webp(): void
    {
        $this->format('webp');
    }

    /**
     * Convert to AVIF format (requires server support).
     */
    public function avif(): void
    {
        $this->format('avif');
    }

    /**
     * Apply blur effect.
     *
     * @param int $radius Blur radius
     * @param int|null $sigma Optional sigma value
     */
    public function blur(int $radius, ?int $sigma = null): void
    {
        if ($sigma !== null) {
            $this->addFilter('blur', $radius, $sigma);
        } else {
            $this->addFilter('blur', $radius);
        }
    }

    /**
     * Adjust brightness.
     *
     * @param int $amount Brightness adjustment (-100 to 100)
     */
    public function brightness(int $amount): void
    {
        $this->addFilter('brightness', $amount);
    }

    /**
     * Adjust contrast.
     *
     * @param int $amount Contrast adjustment (-100 to 100)
     */
    public function contrast(int $amount): void
    {
        $this->addFilter('contrast', $amount);
    }

    /**
     * Convert to grayscale.
     */
    public function grayscale(): void
    {
        $this->addFilter('grayscale');
    }

    /**
     * Rotate the image.
     *
     * @param int $angle Rotation angle (0, 90, 180, 270)
     */
    public function rotate(int $angle): void
    {
        $this->addFilter('rotate', $angle);
    }

    /**
     * Apply sharpen effect.
     *
     * @param float $amount Sharpen amount
     * @param float $radius Sharpen radius
     * @param bool $luminanceOnly Apply to luminance only
     */
    public function sharpen(float $amount, float $radius, bool $luminanceOnly = false): void
    {
        $this->addFilter('sharpen', $amount, $radius, $luminanceOnly);
    }

    /**
     * Add noise to the image.
     *
     * @param int $amount Noise amount (0-100)
     */
    public function noise(int $amount): void
    {
        $this->addFilter('noise', $amount);
    }

    /**
     * Add a watermark.
     *
     * @param string $imageUrl URL of the watermark image
     * @param int $x X position (negative = from right)
     * @param int $y Y position (negative = from bottom)
     * @param int $alpha Transparency (0-100, 0 = fully visible)
     */
    public function watermark(string $imageUrl, int $x = 0, int $y = 0, int $alpha = 0): void
    {
        $this->addFilter('watermark', $imageUrl, $x, $y, $alpha);
    }

    /**
     * Fill empty space with a color (use with fit-in).
     *
     * @param string $color Color (hex without #, 'auto', 'blur', or 'transparent')
     */
    public function fill(string $color): void
    {
        $this->addFilter('fill', $color);
    }

    /**
     * Apply rounded corners.
     *
     * @param int $radius Corner radius
     * @param int|null $red Background red component (0-255)
     * @param int|null $green Background green component (0-255)
     * @param int|null $blue Background blue component (0-255)
     */
    public function roundCorners(int $radius, ?int $red = null, ?int $green = null, ?int $blue = null): void
    {
        if ($red !== null && $green !== null && $blue !== null) {
            $this->addFilter('round_corner', $radius, $red, $green, $blue);
        } else {
            $this->addFilter('round_corner', $radius);
        }
    }

    /**
     * Strip EXIF metadata from the image.
     */
    public function stripExif(): void
    {
        $this->addFilter('strip_exif');
    }

    /**
     * Strip ICC color profile from the image.
     */
    public function stripIcc(): void
    {
        $this->addFilter('strip_icc');
    }

    /**
     * Prevent upscaling of images smaller than requested size.
     */
    public function noUpscale(): void
    {
        $this->addFilter('no_upscale');
    }

    /**
     * Adjust color saturation.
     *
     * @param float $amount Saturation adjustment (0.0 to 2.0, 1.0 = no change)
     */
    public function saturation(float $amount): void
    {
        $this->addFilter('saturation', $amount);
    }

    /**
     * Adjust RGB color channels.
     *
     * @param int $red Red adjustment (-100 to 100)
     * @param int $green Green adjustment (-100 to 100)
     * @param int $blue Blue adjustment (-100 to 100)
     */
    public function rgb(int $red, int $green, int $blue): void
    {
        $this->addFilter('rgb', $red, $green, $blue);
    }

    /**
     * Set maximum file size in bytes.
     *
     * @param int $bytes Maximum file size
     */
    public function maxBytes(int $bytes): void
    {
        $this->addFilter('max_bytes', $bytes);
    }

    /**
     * Apply histogram equalization.
     */
    public function equalize(): void
    {
        $this->addFilter('equalize');
    }

    /**
     * Apply convolution matrix.
     *
     * @param array<int, int|float> $matrix Convolution matrix values
     * @param int $columns Number of columns in the matrix
     * @param bool $normalize Whether to normalize the result
     */
    public function convolution(array $matrix, int $columns, bool $normalize = false): void
    {
        $matrixStr = implode(';', $matrix);
        $this->addFilter('convolution', $matrixStr, $columns, $normalize);
    }

    /**
     * Request JSON metadata instead of the thumbnailed image.
     */
    public function metadataOnly(bool $metadataOnly): void
    {
        $this->metadataOnly = $metadataOnly;
    }

    /**
     * Convert commands to an array of URL path segments.
     *
     * @return array<int, string>
     */
    public function toArray(): array
    {
        $commands = [];

        if ($this->metadataOnly) {
            $commands[] = 'meta';
        }

        if ($this->trim !== null) {
            $commands[] = $this->trim;
        }

        if ($this->crop !== null) {
            $commands[] = $this->crop;
        }

        if ($this->resize !== null) {
            $resizeCmd = $this->resize;

            // Handle flipping via negative dimensions
            if ($this->flipHorizontal || $this->flipVertical) {
                // For fit-in variants, we need to modify the dimensions part
                if (preg_match('/^((?:adaptive-)?(?:full-)?fit-in\/)(-?\d+)x(-?\d+)$/', $resizeCmd, $matches)) {
                    $prefix = $matches[1];
                    $width = (int) $matches[2];
                    $height = (int) $matches[3];

                    if ($this->flipHorizontal) {
                        $width = -abs($width);
                    }
                    if ($this->flipVertical) {
                        $height = -abs($height);
                    }

                    $resizeCmd = "{$prefix}{$width}x{$height}";
                } elseif (preg_match('/^(-?\d+|orig)x(-?\d+|orig)$/', $resizeCmd, $matches)) {
                    $width = $matches[1];
                    $height = $matches[2];

                    if ($this->flipHorizontal && $width !== 'orig') {
                        $width = -abs((int) $width);
                    }
                    if ($this->flipVertical && $height !== 'orig') {
                        $height = -abs((int) $height);
                    }

                    $resizeCmd = "{$width}x{$height}";
                }
            }

            $commands[] = $resizeCmd;
        }

        if ($this->halign !== null) {
            $commands[] = $this->halign;
        }

        if ($this->valign !== null) {
            $commands[] = $this->valign;
        }

        if ($this->smartCrop) {
            $commands[] = 'smart';
        }

        if (count($this->filters) > 0) {
            $commands[] = 'filters:' . implode(':', $this->filters);
        }

        return $commands;
    }
}
