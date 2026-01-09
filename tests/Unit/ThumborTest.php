<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use R0bdiabl0\Thumbor\Thumbor;
use R0bdiabl0\Thumbor\UrlBuilder;

class ThumborTest extends TestCase
{
    private string $server = 'http://thumbor.example.com';
    private string $secret = 'my-secret-key';
    private string $testImage = 'https://example.com/images/test.jpg';

    public function test_can_create_instance(): void
    {
        $thumbor = new Thumbor($this->server, $this->secret);

        $this->assertInstanceOf(Thumbor::class, $thumbor);
    }

    public function test_can_create_instance_with_construct_method(): void
    {
        $thumbor = Thumbor::construct($this->server, $this->secret);

        $this->assertInstanceOf(Thumbor::class, $thumbor);
    }

    public function test_url_returns_url_builder(): void
    {
        $thumbor = new Thumbor($this->server, $this->secret);
        $builder = $thumbor->url($this->testImage);

        $this->assertInstanceOf(UrlBuilder::class, $builder);
    }

    public function test_get_server_returns_server_url(): void
    {
        $thumbor = new Thumbor($this->server, $this->secret);

        $this->assertEquals($this->server, $thumbor->getServer());
    }

    public function test_has_secret_returns_true_when_secret_is_set(): void
    {
        $thumbor = new Thumbor($this->server, $this->secret);

        $this->assertTrue($thumbor->hasSecret());
    }

    public function test_has_secret_returns_false_when_secret_is_null(): void
    {
        $thumbor = new Thumbor($this->server, null);

        $this->assertFalse($thumbor->hasSecret());
    }

    public function test_has_secret_returns_false_when_secret_is_empty_string(): void
    {
        $thumbor = new Thumbor($this->server, '');

        $this->assertFalse($thumbor->hasSecret());
    }
}
