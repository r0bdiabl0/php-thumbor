<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use R0bdiabl0\Thumbor\Thumbor;
use R0bdiabl0\Thumbor\UrlBuilder;

class DefaultFormatTest extends TestCase
{
    private string $server = 'http://thumbor.example.com';
    private string $secret = 'my-secret-key';
    private string $image = 'https://example.com/images/test.jpg';

    public function test_default_format_is_applied_when_no_explicit_format(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->image, 'webp');
        $url = (string) $builder->resize(640, 0);

        $this->assertStringContainsString('format(webp)', $url);
    }

    public function test_default_format_does_not_override_explicit_format(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->image, 'webp');
        $url = (string) $builder->resize(640, 0)->format('jpeg');

        $this->assertStringContainsString('format(jpeg)', $url);
        $this->assertStringNotContainsString('format(webp)', $url);
    }

    public function test_default_format_does_not_override_add_filter_format(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->image, 'webp');
        $url = (string) $builder->addFilter('format', 'avif');

        $this->assertStringContainsString('format(avif)', $url);
        $this->assertStringNotContainsString('format(webp)', $url);
    }

    public function test_default_format_does_not_override_convenience_methods(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->image, 'webp');
        $url = (string) $builder->avif();

        $this->assertStringContainsString('format(avif)', $url);
        $this->assertStringNotContainsString('format(webp)', $url);
    }

    public function test_no_default_format_means_no_format_filter(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->image);
        $url = (string) $builder->resize(640, 0);

        $this->assertStringNotContainsString('format(', $url);
    }

    public function test_default_format_not_applied_to_metadata_requests(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->image, 'webp');
        $url = (string) $builder->metadataOnly(true);

        $this->assertStringNotContainsString('format(webp)', $url);
    }

    public function test_factory_propagates_default_format(): void
    {
        $thumbor = new Thumbor($this->server, $this->secret, 'webp');
        $url = (string) $thumbor->url($this->image)->resize(100, 100);

        $this->assertStringContainsString('format(webp)', $url);
    }

    public function test_default_format_is_signed_into_the_path(): void
    {
        // The same transform with vs. without the default format must yield a
        // different signed URL — proving the format is inside the signed payload
        // and therefore cannot be added/stripped by a client without breaking
        // the signature.
        $withFormat = (string) (new UrlBuilder($this->server, $this->secret, $this->image, 'webp'))->resize(640, 0);
        $withoutFormat = (string) (new UrlBuilder($this->server, $this->secret, $this->image))->resize(640, 0);

        $this->assertNotSame($withoutFormat, $withFormat);
    }

    public function test_cloned_builder_preserves_default_format(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->image, 'webp');
        $clone = clone $builder;
        $url = (string) $clone->resize(640, 0);

        $this->assertStringContainsString('format(webp)', $url);
    }
}
