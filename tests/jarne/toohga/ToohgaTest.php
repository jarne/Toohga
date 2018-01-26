<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 08.03.17
 * Time: 20:55
 */

namespace jarne\toohga;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class ToohgaTest extends TestCase {
    /**
     * Test the static home page
     */
    public function testGeneralPage(): void {
        $toohga = new Toohga();
        $output = $toohga->process(
            array(
                "REMOTE_ADDR" => "123.456.123.456",
                "HTTP_HOST" => "example.org",
                "REQUEST_URI" => "",
                "REQUEST_METHOD" => "GET"
            ),
            array()
        );

        $crawler = new Crawler($output);

        $this->assertEquals("Toohga", $crawler->filterXPath("//h1")->text());
        $this->assertEquals("A simple URL shortener", $crawler->filterXPath("//h2")->text());
    }

    /**
     * Try to create a new shortened URL
     *
     * @return string
     *
     * @runInSeparateProcess
     */
    public function testCreateUrl(): string {
        $toohga = new Toohga();
        $output = $toohga->process(
            array(
                "REMOTE_ADDR" => "123.456.123.456",
                "HTTP_HOST" => "example.org",
                "REQUEST_URI" => "",
                "REQUEST_METHOD" => "POST"
            ),
            array(
                "longUrl" => "https://example.org/category/another.html"
            )
        );

        $data = json_decode($output);

        $this->assertEquals("success", $data->status);
        $this->assertTrue(isset($data->shortUrl));

        $shortUrlParts = explode("/", $data->shortUrl);

        $this->assertCount(4, $shortUrlParts);

        return $shortUrlParts[3];
    }

    /**
     * Try to open the recently created URL
     *
     * @param string $shortId
     *
     * @runInSeparateProcess
     * @depends testCreateUrl
     */
    public function testOpenUrl(string $shortId): void {
        $toohga = new Toohga();
        $toohga->process(
            array(
                "REMOTE_ADDR" => "123.456.123.456",
                "HTTP_HOST" => "example.org",
                "REQUEST_URI" => "/" . $shortId,
                "REQUEST_METHOD" => "GET"
            ),
            array()
        );

        $this->assertContains("Location: https://example.org/category/another.html", xdebug_get_headers());
    }

    /**
     * Test if the JSON header is working
     *
     * @runInSeparateProcess
     */
    public function testWillReturnJson(): void {
        $toohga = new Toohga();
        $toohga->willReturnJson();

        $this->assertContains("Content-type: application/json", xdebug_get_headers());
    }

    /**
     * Test if the JSON header is working
     *
     * @runInSeparateProcess
     */
    public function testRedirectTo(): void {
        $toohga = new Toohga();
        $toohga->redirectTo("https://example.com/something/anything.html");

        $this->assertContains("Location: https://example.com/something/anything.html", xdebug_get_headers());
    }
}