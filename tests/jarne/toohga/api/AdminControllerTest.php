<?php

/**
 * Toohga | Admin controller tests
 */

namespace jarne\toohga\tests\api;

/**
 * @coversDefaultClass \jarne\toohga\api\AdminController
 */
class AdminControllerTest extends APITestCase
{
    /**
     * @var string JWT token for test requests
     */
    private string $jwtToken;

    /**
     * Obtain JWT token for requests accessing the admin API
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $req = $this->request("POST", "/admin/api/auth")
            ->withParsedBody(array(
                "admin_key" => getenv("ADMIN_KEY")
            ));

        $resp = $this->getApp()->handle($req);

        $bodyData = json_decode((string)$resp->getBody());
        $this->jwtToken = $bodyData->jwt;
    }

    /**
     * Test authentication route for successfully obtaining a token
     *
     * @covers ::authenticate
     */
    public function testTokenReqSuccess(): void
    {
        $req = $this->request("POST", "/admin/api/auth")
            ->withParsedBody(array(
                "admin_key" => getenv("ADMIN_KEY")
            ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertIsString($bodyData->jwt);
    }

    /**
     * Test authentication route with wrong secret key
     *
     * @covers ::authenticate
     */
    public function testTokenReqInvalidCreds(): void
    {
        $req = $this->request("POST", "/admin/api/auth")
            ->withParsedBody(array(
                "admin_key" => "invalidKey123"
            ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(401, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertEquals("invalid_credentials", $bodyData->error->code);
    }

    /**
     * Test URL list overview API
     *
     * @covers ::getUrlList
     */
    public function testGetUrlList(): void
    {
        $req = $this->request("GET", "/admin/api/url")
            ->withHeader("Authorization", "Bearer " . $this->jwtToken);

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertIsArray($bodyData->short_urls);

        $this->assertArrayHasKey(0, $bodyData->short_urls);
        $this->assertArrayHasKey(1, $bodyData->short_urls);
        $this->assertArrayNotHasKey(2, $bodyData->short_urls);

        $this->assertEquals(0, $bodyData->short_urls[0]->id);
        $this->assertEquals("https://www.php.net/manual/de/language.oop5.traits.php", $bodyData->short_urls[0]->target);
        $this->assertEquals("123.123.123.123", $bodyData->short_urls[0]->client);
        $this->assertEquals("1", $bodyData->short_urls[0]->shortId);
    }

    /**
     * Test URL delete API
     *
     * @covers ::deleteUrl
     */
    public function testDeleteUrl(): void
    {
        $req = $this->request("DELETE", "/admin/api/url/123")
            ->withHeader("Authorization", "Bearer " . $this->jwtToken);

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(204, $resp->getStatusCode());
    }

    /**
     * Test URL cleanup API
     *
     * @covers ::cleanupUrls
     */
    public function testCleanupUrls(): void
    {
        $req = $this->request("POST", "/admin/api/urlCleanup")
            ->withHeader("Authorization", "Bearer " . $this->jwtToken);

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(204, $resp->getStatusCode());
    }

    /**
     * Test user list API
     *
     * @covers ::getUserList
     */
    public function testGetUserList(): void
    {
        $req = $this->request("GET", "/admin/api/user")
            ->withHeader("Authorization", "Bearer " . $this->jwtToken);

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertIsArray($bodyData->users);

        $this->assertArrayHasKey(0, $bodyData->users);
        $this->assertArrayHasKey(1, $bodyData->users);
        $this->assertArrayNotHasKey(2, $bodyData->users);

        $this->assertEquals(0, $bodyData->users[0]->id);
        $this->assertEquals("123abc", $bodyData->users[0]->upin);
        $this->assertEquals("Test user", $bodyData->users[0]->displayName);
    }

    /**
     * Test user create API
     *
     * @covers ::createUser
     */
    public function testCreateUser(): void
    {
        $req = $this->request("POST", "/admin/api/user")
            ->withHeader("Authorization", "Bearer " . $this->jwtToken)
            ->withHeader("Content-Type", "application/json")
            ->withParsedBody(array(
                "uniquePin" => "abc876test",
                "displayName" => "User 123"
            ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(204, $resp->getStatusCode());
    }

    /**
     * Test user delete API
     *
     * @covers ::deleteUser
     */
    public function testDeleteUser(): void
    {
        $req = $this->request("DELETE", "/admin/api/user/5")
            ->withHeader("Authorization", "Bearer " . $this->jwtToken);

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(204, $resp->getStatusCode());
    }
}
