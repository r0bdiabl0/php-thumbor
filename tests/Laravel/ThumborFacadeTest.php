<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor\Tests\Laravel;

use R0bdiabl0\Thumbor\Laravel\Facades\Thumbor;
use R0bdiabl0\Thumbor\UrlBuilder;

class ThumborFacadeTest extends TestCase
{
    private string $testImage = 'https://example.com/images/test.jpg';

    public function test_facade_resolves_to_thumbor_factory(): void
    {
        $builder = Thumbor::url($this->testImage);

        $this->assertInstanceOf(UrlBuilder::class, $builder);
    }

    public function test_facade_generates_url_with_resize(): void
    {
        $url = (string) Thumbor::url($this->testImage)->resize(640, 480);

        $this->assertStringContainsString('640x480', $url);
    }

    public function test_facade_generates_url_with_fit_in(): void
    {
        $url = (string) Thumbor::url($this->testImage)->fitIn(800, 600);

        $this->assertStringContainsString('fit-in', $url);
        $this->assertStringContainsString('800x600', $url);
    }

    public function test_facade_generates_url_with_smart_crop(): void
    {
        $url = (string) Thumbor::url($this->testImage)->smartCrop(true);

        $this->assertStringContainsString('smart', $url);
    }

    public function test_facade_generates_url_with_filter(): void
    {
        $url = (string) Thumbor::url($this->testImage)->addFilter('brightness', '50');

        $this->assertStringContainsString('filters:brightness(50)', $url);
    }

    public function test_facade_generates_url_with_multiple_filters(): void
    {
        $url = (string) Thumbor::url($this->testImage)
            ->addFilter('brightness', '50')
            ->addFilter('contrast', '20');

        $this->assertStringContainsString('brightness(50)', $url);
        $this->assertStringContainsString('contrast(20)', $url);
    }

    public function test_facade_generates_url_with_chained_operations(): void
    {
        $url = (string) Thumbor::url($this->testImage)
            ->fitIn(640, 480)
            ->smartCrop(true)
            ->addFilter('quality', '80');

        $this->assertStringContainsString('fit-in', $url);
        $this->assertStringContainsString('640x480', $url);
        $this->assertStringContainsString('smart', $url);
        $this->assertStringContainsString('quality(80)', $url);
    }

    public function test_facade_get_server(): void
    {
        $server = Thumbor::getServer();

        $this->assertEquals('http://thumbor.example.com', $server);
    }

    public function test_facade_has_secret(): void
    {
        $this->assertTrue(Thumbor::hasSecret());
    }
}
