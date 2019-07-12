<?php
/**
 * Toohga | main tests file
 */

namespace jarne\toohga;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Process\Process;

class ToohgaTest extends TestCase {
    /* @var Process */
    private static $process;

    /**
     * Setup testing environment
     */
    public static function setUpBeforeClass(): void {
        static::$process = new Process(array(
            "php",
            "-S",
            "localhost:8080",
            "-t",
            "."
        ));
        static::$process->start();

        usleep(100000);
    }

    /**
     * Shutdown testing environment
     */
    public static function tearDownAfterClass(): void {
        static::$process->stop();
    }

    /**
     * Test the static home page
     *
     * @throws GuzzleException
     */
    public function testGeneralPage(): void {
        $client = new Client(array(
            "base_uri" => "http://localhost:8080"
        ));

        $response = $client->request("GET", "/");

        $this->assertEquals(200, $response->getStatusCode());

        $contents = $response->getBody()->getContents();

        $crawler = new Crawler($contents);

        $this->assertEquals("Toohga", $crawler->filterXPath("//h1")->text());
        $this->assertEquals("A simple URL shortener", $crawler->filterXPath("//h2")->text());
    }

    /**
     * Try to create a new shortened URL
     *
     * @throws GuzzleException
     *
     * @return string
     */
    public function testCreateUrl(): string {
        $client = new Client(array(
            "base_uri" => "http://localhost:8080"
        ));

        $response = $client->request("POST", "/", array(
            "form_params" => array(
                "longUrl" => "https://www.example.com/category/another51.html"
            )
        ));

        $this->assertEquals(200, $response->getStatusCode());

        $contents = $response->getBody()->getContents();

        $data = json_decode($contents);

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
     * @throws GuzzleException
     *
     * @depends testCreateUrl
     */
    public function testOpenUrl(string $shortId): void {
        $client = new Client(array(
            "base_uri" => "http://localhost:8080"
        ));

        $response = $client->request("GET", "/" . $shortId, array(
            "allow_redirects" => false
        ));

        $this->assertEquals(302, $response->getStatusCode());

        $locationHeaders = $response->getHeader("Location");

        $this->assertEquals(1, count($locationHeaders));
        $this->assertEquals("https://www.example.com/category/another51.html", $locationHeaders[0]);
    }
}
