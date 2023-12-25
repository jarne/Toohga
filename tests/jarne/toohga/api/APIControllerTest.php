<?php

/**
 * Toohga | API controller tests
 */

namespace jarne\toohga\tests\api;

/**
 * @coversDefaultClass \jarne\toohga\api\APIController
 */
class APIControllerTest extends APITestCase
{
    /**
     * Test calling a short URL
     *
     * @covers ::get
     */
    public function testGet(): void
    {
        $req = $this->request("GET", "/b");
        $resp = $this->getApp()->handle($req);

        $this->assertEquals(302, $resp->getStatusCode());
        $this->assertEquals(
            "https://github.com/jarne/Toohga/blob/master/src/jarne/toohga/api/APIController.php",
            $resp->getHeaderLine("Location")
        );
    }

    /**
     * Test creating a new short URL entry (without auth)
     *
     * @covers ::create
     */
    public function testCreate(): void
    {
        putenv("TGA_AUTH_REQUIRED=false");

        $req = $this->request("POST", "/api/create", array(
            "REMOTE_ADDR" => "123.123.123.123",
            "SERVER_NAME" => "localhost",
            "SERVER_PORT" => 80
        ))
            ->withHeader("Content-Type", "application/json")
            ->withParsedBody(array(
                "longUrl" => "https://github.com/jarne/Toohga/blob/master/src/jarne/toohga/api/APIController.php"
            ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertEquals("http://localhost/a", $bodyData->short);
    }

    /**
     * Test creating a new short URL entry with wrong credentials
     *
     * @covers ::create
     */
    public function testCreateWithFailedAuth(): void
    {
        putenv("TGA_AUTH_REQUIRED=true");

        $req = $this->request("POST", "/api/create", array(
            "REMOTE_ADDR" => "123.123.123.123",
            "SERVER_NAME" => "localhost",
            "SERVER_PORT" => 80
        ))
            ->withHeader("Content-Type", "application/json")
            ->withParsedBody(array(
                "longUrl" => "https://github.com/jarne/Toohga/blob/master/src/jarne/toohga/api/APIController.php"
            ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertEquals("auth_failed", $bodyData->error->code);
    }

    /**
     * Test creating a new short URL entry with authentication enabled
     *
     * @covers ::create
     */
    public function testCreateWithAuth(): void
    {
        putenv("TGA_AUTH_REQUIRED=true");

        $req = $this->request("POST", "/api/create", array(
            "REMOTE_ADDR" => "123.123.123.123",
            "SERVER_NAME" => "localhost",
            "SERVER_PORT" => 80
        ))
            ->withHeader("Content-Type", "application/json")
            ->withParsedBody(array(
                "longUrl" => "https://github.com/jarne/Toohga/blob/master/src/jarne/toohga/api/APIController.php",
                "userPin" => "test456"
            ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertEquals("http://localhost/a", $bodyData->short);
    }
}
