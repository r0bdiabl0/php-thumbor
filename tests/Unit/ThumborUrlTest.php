<?php

declare(strict_types=1);

namespace R0bdiabl0\Thumbor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use R0bdiabl0\Thumbor\ThumborUrl;

class ThumborUrlTest extends TestCase
{
    private string $server = 'http://thumbor.example.com';
    private string $secret = 'my-secret-key';
    private string $testImage = 'https://example.com/images/test.jpg';

    public function test_build_generates_signed_url(): void
    {
        $url = new ThumborUrl($this->server, $this->secret, $this->testImage, []);
        $result = $url->build();

        $this->assertStringStartsWith($this->server, $result);
        $this->assertStringContainsString($this->testImage, $result);
        $this->assertStringNotContainsString('unsafe', $result);
    }

    public function test_build_generates_unsafe_url_when_no_secret(): void
    {
        $url = new ThumborUrl($this->server, null, $this->testImage, []);
        $result = $url->build();

        $this->assertStringContainsString('unsafe', $result);
    }

    public function test_build_generates_unsafe_url_when_empty_secret(): void
    {
        $url = new ThumborUrl($this->server, '', $this->testImage, []);
        $result = $url->build();

        $this->assertStringContainsString('unsafe', $result);
    }

    public function test_build_includes_commands_in_path(): void
    {
        $commands = ['fit-in/640x480', 'smart'];
        $url = new ThumborUrl($this->server, $this->secret, $this->testImage, $commands);
        $result = $url->build();

        $this->assertStringContainsString('fit-in/640x480', $result);
        $this->assertStringContainsString('smart', $result);
    }

    public function test_to_string_returns_same_as_build(): void
    {
        $url = new ThumborUrl($this->server, $this->secret, $this->testImage, ['fit-in/640x480']);

        $this->assertEquals($url->build(), (string) $url);
    }

    public function test_sign_generates_url_safe_signature(): void
    {
        $signature = ThumborUrl::sign('test/path', $this->secret);

        // URL-safe base64 should not contain + or /
        $this->assertStringNotContainsString('+', $signature);
        $this->assertStringNotContainsString('/', $signature);
    }

    public function test_sign_is_deterministic(): void
    {
        $signature1 = ThumborUrl::sign('test/path', $this->secret);
        $signature2 = ThumborUrl::sign('test/path', $this->secret);

        $this->assertEquals($signature1, $signature2);
    }

    public function test_sign_produces_different_signatures_for_different_paths(): void
    {
        $signature1 = ThumborUrl::sign('path/one', $this->secret);
        $signature2 = ThumborUrl::sign('path/two', $this->secret);

        $this->assertNotEquals($signature1, $signature2);
    }

    public function test_sign_produces_different_signatures_for_different_secrets(): void
    {
        $signature1 = ThumborUrl::sign('same/path', 'secret-one');
        $signature2 = ThumborUrl::sign('same/path', 'secret-two');

        $this->assertNotEquals($signature1, $signature2);
    }

    public function test_url_format_is_correct(): void
    {
        $url = new ThumborUrl($this->server, null, $this->testImage, []);
        $result = $url->build();

        // Format should be: server/signature/image
        $expected = $this->server . '/unsafe/' . $this->testImage;
        $this->assertEquals($expected, $result);
    }

    public function test_url_format_with_commands(): void
    {
        $url = new ThumborUrl($this->server, null, $this->testImage, ['fit-in/640x480', 'smart']);
        $result = $url->build();

        // Format should be: server/signature/commands/image
        $this->assertMatchesRegularExpression(
            '#^' . preg_quote($this->server, '#') . '/unsafe/fit-in/640x480/smart/' . preg_quote($this->testImage, '#') . '$#',
            $result
        );
    }
}
