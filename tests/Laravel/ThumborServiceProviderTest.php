<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor\Tests\Laravel;

use R0bdiabl0\Thumbor\Thumbor;
use R0bdiabl0\Thumbor\UrlBuilder;

class ThumborServiceProviderTest extends TestCase
{
    private string $testImage = 'https://example.com/images/test.jpg';

    public function test_service_provider_registers_thumbor_instance(): void
    {
        $thumbor = $this->app->make('thumbor');

        $this->assertInstanceOf(Thumbor::class, $thumbor);
    }

    public function test_thumbor_can_be_resolved_by_class_name(): void
    {
        $thumbor = $this->app->make(Thumbor::class);

        $this->assertInstanceOf(Thumbor::class, $thumbor);
    }

    public function test_thumbor_creates_url_builder(): void
    {
        $thumbor = $this->app->make('thumbor');
        $builder = $thumbor->url($this->testImage);

        $this->assertInstanceOf(UrlBuilder::class, $builder);
    }

    public function test_singleton_returns_same_instance(): void
    {
        $instance1 = $this->app->make('thumbor');
        $instance2 = $this->app->make('thumbor');

        $this->assertSame($instance1, $instance2);
    }

    public function test_config_is_loaded(): void
    {
        $this->assertEquals('http://thumbor.example.com', config('thumbor.server'));
        $this->assertEquals('test-secret-key', config('thumbor.key'));
    }

    public function test_url_builder_generates_signed_url(): void
    {
        $thumbor = $this->app->make('thumbor');
        $url = (string) $thumbor->url($this->testImage)->fitIn(100, 100);

        $this->assertStringContainsString('thumbor.example.com', $url);
        $this->assertStringContainsString('fit-in', $url);
        $this->assertStringContainsString('100x100', $url);
    }
}
