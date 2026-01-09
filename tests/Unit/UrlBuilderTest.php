<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor\Tests\Unit;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;
use R0bdiabl0\Thumbor\ThumborUrl;
use R0bdiabl0\Thumbor\UrlBuilder;

class UrlBuilderTest extends TestCase
{
    private string $server = 'http://thumbor.example.com';
    private string $secret = 'my-secret-key';
    private string $testImage = 'https://example.com/images/test.jpg';

    public function test_build_returns_thumbor_url(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = $builder->build();

        $this->assertInstanceOf(ThumborUrl::class, $url);
    }

    public function test_to_string_returns_url_string(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder;

        $this->assertStringContainsString($this->server, $url);
        $this->assertStringContainsString($this->testImage, $url);
    }

    public function test_fit_in_adds_fit_in_to_url(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder->fitIn(640, 480);

        $this->assertStringContainsString('fit-in', $url);
        $this->assertStringContainsString('640x480', $url);
    }

    public function test_full_fit_in_adds_full_fit_in_to_url(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder->fullFitIn(800, 600);

        $this->assertStringContainsString('full-fit-in', $url);
        $this->assertStringContainsString('800x600', $url);
    }

    public function test_resize_adds_dimensions_to_url(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder->resize(320, 240);

        $this->assertStringContainsString('320x240', $url);
    }

    public function test_smart_crop_adds_smart_to_url(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder->smartCrop(true);

        $this->assertStringContainsString('smart', $url);
    }

    public function test_add_filter_adds_filter_to_url(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder->addFilter('brightness', 50);

        $this->assertStringContainsString('filters:brightness(50)', $url);
    }

    public function test_multiple_filters_are_chained(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder
            ->addFilter('brightness', 50)
            ->addFilter('contrast', 20);

        $this->assertStringContainsString('brightness(50)', $url);
        $this->assertStringContainsString('contrast(20)', $url);
    }

    public function test_crop_adds_crop_coordinates_to_url(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder->crop(10, 20, 100, 200);

        $this->assertStringContainsString('10x20:100x200', $url);
    }

    public function test_halign_adds_horizontal_alignment_to_url(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder->halign('left');

        $this->assertStringContainsString('left', $url);
    }

    public function test_valign_adds_vertical_alignment_to_url(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder->valign('top');

        $this->assertStringContainsString('top', $url);
    }

    public function test_metadata_only_adds_meta_to_url(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder->metadataOnly(true);

        $this->assertStringContainsString('/meta/', $url);
    }

    public function test_trim_adds_trim_to_url(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder->trim();

        $this->assertStringContainsString('trim', $url);
    }

    public function test_trim_with_colour_source_adds_colour_source(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder->trim('bottom-right');

        $this->assertStringContainsString('trim:bottom-right', $url);
    }

    public function test_chained_operations(): void
    {
        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $url = (string) $builder
            ->fitIn(640, 480)
            ->smartCrop(true)
            ->addFilter('quality', 80)
            ->addFilter('blur', 2);

        $this->assertStringContainsString('fit-in', $url);
        $this->assertStringContainsString('640x480', $url);
        $this->assertStringContainsString('smart', $url);
        $this->assertStringContainsString('quality(80)', $url);
        $this->assertStringContainsString('blur(2)', $url);
    }

    public function test_builder_is_clonable(): void
    {
        $builder1 = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $builder1->fitIn(640, 480);

        $builder2 = clone $builder1;
        $builder2->addFilter('quality', 80);

        $url1 = (string) $builder1;
        $url2 = (string) $builder2;

        $this->assertStringNotContainsString('quality', $url1);
        $this->assertStringContainsString('quality', $url2);
    }

    public function test_invalid_method_throws_exception(): void
    {
        $this->expectException(BadMethodCallException::class);

        $builder = new UrlBuilder($this->server, $this->secret, $this->testImage);
        $builder->invalidMethod(); // @phpstan-ignore-line
    }
}
