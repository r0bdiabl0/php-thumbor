# Thumbor PHP

A modern PHP library for generating [Thumbor](https://github.com/thumbor/thumbor) image URLs. Works standalone or with Laravel integration.

[![Tests](https://github.com/r0bdiabl0/php-thumbor/actions/workflows/tests.yml/badge.svg)](https://github.com/r0bdiabl0/php-thumbor/actions/workflows/tests.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://www.php.net/)

## Features

- **Zero dependencies** - Pure PHP implementation, no external packages required
- **Laravel optional** - Works standalone or with full Laravel integration
- **Modern PHP** - Built for PHP 8.2+ with strict types and readonly properties
- **Fluent API** - Chainable methods for building complex image transformations
- **Secure URLs** - HMAC-SHA1 signed URLs for production security
- **Full Thumbor support** - Resize, crop, filters, smart cropping, and more

## Requirements

- PHP 8.2 or higher
- Thumbor 6.0+ (7.0+ recommended for AVIF/WebP/HEIC support)
- Laravel 10, 11, 12, or 13 (optional, for Laravel integration)

## Installation

```bash
composer require r0bdiablo/php-thumbor
```

## Quick Start

### Standalone Usage (No Framework)

```php
use R0bdiabl0\Thumbor\Thumbor;

// Create a Thumbor instance with your server URL and secret key
$thumbor = new Thumbor('https://thumbor.example.com', 'your-secret-key');

// Generate image URLs
$url = $thumbor->url('https://example.com/images/photo.jpg')
    ->fitIn(640, 480)
    ->smartCrop(true)
    ->addFilter('quality', 80);

echo $url;
// Output: https://thumbor.example.com/SIGNATURE/fit-in/640x480/smart/filters:quality(80)/https://example.com/images/photo.jpg
```

### Laravel Usage

The package auto-registers with Laravel 10+. Just publish the config:

```bash
php artisan vendor:publish --tag=thumbor-config
```

Configure your `.env`:

```env
THUMBOR_SERVER=https://thumbor.example.com
THUMBOR_KEY=your-secret-key
```

Use the facade:

```php
use R0bdiabl0\Thumbor\Laravel\Facades\Thumbor;

$url = Thumbor::url('https://example.com/images/photo.jpg')
    ->fitIn(640, 480)
    ->addFilter('quality', 80);
```

Or inject the service:

```php
use R0bdiabl0\Thumbor\Thumbor;

class ImageController extends Controller
{
    public function __construct(private Thumbor $thumbor) {}

    public function thumbnail(string $imageUrl): string
    {
        return (string) $this->thumbor->url($imageUrl)->fitIn(200, 200);
    }
}
```

## API Reference

### Resizing

```php
// Fit image within dimensions (maintains aspect ratio)
$thumbor->url($image)->fitIn(640, 480);

// Fit by smallest side
$thumbor->url($image)->fullFitIn(800, 600);

// Exact resize (may distort)
$thumbor->url($image)->resize(320, 240);

// Proportional resize (0 = auto-calculate)
$thumbor->url($image)->resize(320, 0);  // Width 320, height auto
```

### Cropping

```php
// Manual crop coordinates
$thumbor->url($image)->crop(10, 20, 200, 150);

// Smart crop (face/feature detection)
$thumbor->url($image)->smartCrop(true);

// Alignment when cropping
$thumbor->url($image)->halign('left');   // left, center, right
$thumbor->url($image)->valign('top');    // top, middle, bottom
```

### Filters

```php
// Quality (JPEG)
$thumbor->url($image)->addFilter('quality', 80);

// Brightness (-100 to 100)
$thumbor->url($image)->addFilter('brightness', 20);

// Contrast (-100 to 100)
$thumbor->url($image)->addFilter('contrast', 10);

// Blur
$thumbor->url($image)->addFilter('blur', 5);

// Grayscale
$thumbor->url($image)->addFilter('grayscale');

// Format conversion
$thumbor->url($image)->addFilter('format', 'webp');

// Chain multiple filters
$thumbor->url($image)
    ->addFilter('quality', 80)
    ->addFilter('brightness', 10)
    ->addFilter('sharpen', 1.5, 0.5, true);
```

### Convenience Methods

The library provides convenience methods for common filters:

```php
// Image adjustments
$thumbor->url($image)->quality(80);           // JPEG quality (1-100)
$thumbor->url($image)->brightness(20);        // -100 to 100
$thumbor->url($image)->contrast(10);          // -100 to 100
$thumbor->url($image)->saturation(1.2);       // 0.0 to 2.0
$thumbor->url($image)->grayscale();

// Effects
$thumbor->url($image)->blur(5);               // Radius
$thumbor->url($image)->blur(5, 2);            // Radius + sigma
$thumbor->url($image)->sharpen(1.5, 0.5);     // Amount, radius
$thumbor->url($image)->noise(20);             // 0-100
$thumbor->url($image)->rotate(90);            // 0, 90, 180, 270

// Format conversion
$thumbor->url($image)->webp();                // Convert to WebP
$thumbor->url($image)->avif();                // Convert to AVIF [v7+]
$thumbor->url($image)->format('jpeg');        // Any format

// Utilities
$thumbor->url($image)->stripExif();           // Remove EXIF metadata
$thumbor->url($image)->stripIcc();            // Remove ICC profile
$thumbor->url($image)->noUpscale();           // Prevent upscaling
$thumbor->url($image)->maxBytes(50000);       // Limit file size

// Overlays
$thumbor->url($image)->watermark($watermarkUrl, 10, 10, 50);  // URL, x, y, alpha
$thumbor->url($image)->fill('auto');          // Fill color (hex/auto/blur/transparent)
$thumbor->url($image)->roundCorners(10);      // Border radius
```

### Thumbor Version Compatibility

Most features work with Thumbor 6.0+. The following require **Thumbor 7.0+**:

| Feature | Method | Thumbor Version |
|---------|--------|-----------------|
| AVIF format | `avif()`, `format('avif')` | 7.0+ |
| HEIC format | `format('heic')` | 7.0+ |
| Max bytes | `maxBytes()` | 7.0+ |
| Saturation | `saturation()` | 7.0+ |
| Equalize | `equalize()` | 7.0+ |

> **Note:** Using v7+ features on older Thumbor servers will result in the filter being ignored or an error from Thumbor.

### Other Operations

```php
// Trim whitespace
$thumbor->url($image)->trim();
$thumbor->url($image)->trim('bottom-right', 50);  // With tolerance

// Get metadata only (JSON)
$thumbor->url($image)->metadataOnly(true);
```

### Chaining

All methods are chainable:

```php
$url = $thumbor->url('https://example.com/photo.jpg')
    ->fitIn(640, 480)
    ->smartCrop(true)
    ->addFilter('quality', 85)
    ->addFilter('brightness', 5)
    ->addFilter('format', 'webp');
```

## Configuration

### Laravel Config (`config/thumbor.php`)

```php
return [
    'server' => env('THUMBOR_SERVER', 'http://localhost:8888'),
    'key' => env('THUMBOR_KEY'),  // null for unsafe URLs
];
```

### Environment Variables

```env
THUMBOR_SERVER=https://thumbor.example.com
THUMBOR_KEY=your-secret-key
```

## Unsafe URLs

For development or when security isn't required, you can generate unsigned URLs by omitting the secret key:

```php
$thumbor = new Thumbor('https://thumbor.example.com');  // No secret
$url = $thumbor->url($image)->fitIn(640, 480);
// Output includes "unsafe" instead of signature
```

**Warning:** Never use unsafe URLs in production as they allow anyone to generate arbitrary image transformations.

## Migration from r0bdiablo/laravel5-phumbor

This package replaces the deprecated `r0bdiablo/laravel5-phumbor` package. To migrate:

1. Update your `composer.json`:
   ```bash
   composer remove r0bdiablo/laravel5-phumbor
   composer require r0bdiablo/php-thumbor
   ```

2. Update namespace imports:
   ```php
   // Old
   use R0bdiabl0\Laravel5Phumbor\Facades\Phumbor;

   // New
   use R0bdiabl0\Thumbor\Laravel\Facades\Thumbor;
   ```

3. Update config file name:
   - Old: `config/laravel5-phumbor.php`
   - New: `config/thumbor.php`

4. Update config keys:
   - Old: `laravel5-phumbor.server`, `laravel5-phumbor.key`
   - New: `thumbor.server`, `thumbor.key`

5. Update env variables:
   - Old: (custom)
   - New: `THUMBOR_SERVER`, `THUMBOR_KEY`

The API methods remain the same.

## Testing

```bash
# Run all tests
composer test

# Run unit tests only (no Laravel dependency)
vendor/bin/phpunit --testsuite Unit

# Run Laravel integration tests
vendor/bin/phpunit --testsuite Laravel

# Static analysis
vendor/bin/phpstan analyse
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

- [Robert Pettique](https://github.com/r0bdiabl0)
- Inspired by [99designs/phumbor](https://github.com/99designs/phumbor)
- Built for [Thumbor](https://github.com/thumbor/thumbor)
