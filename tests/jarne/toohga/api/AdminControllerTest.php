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
     * Test admin panel auth request
     *
     * @covers ::panel
     */
    public function testPanelAuthReq(): void
    {
        $req = $this->request("GET", "/admin");

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(401, $resp->getStatusCode());
        $this->assertStringContainsString("Basic realm", $resp->getHeaderLine("WWW-Authenticate"));
    }

    /**
     * Test admin panel with wrong password
     *
     * @covers ::panel
     */
    public function testPanelWrongAuth(): void
    {
        $req = $this->request("GET", "/admin", array(
            "PHP_AUTH_PW" => "wrongPW123"
        ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(401, $resp->getStatusCode());
        $this->assertEquals("", $resp->getHeaderLine("WWW-Authenticate"));
    }

    /**
     * Test admin panel with correct password
     *
     * @covers ::panel
     */
    public function testPanelSuccessAuth(): void
    {
        $req = $this->request("GET", "/admin", array(
            "PHP_AUTH_PW" => getenv("ADMIN_KEY")
        ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());

        $body = (string)$resp->getBody();
        $this->assertStringContainsString("Welcome to the Toogha admin center", $body);
    }

    /**
     * Test URL list overview API
     *
     * @covers ::getUrlList
     */
    public function testGetUrlList(): void
    {
        $req = $this->request("GET", "/admin/api/url", array(
            "PHP_AUTH_PW" => getenv("ADMIN_KEY")
        ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertEquals("success", $bodyData->status);
        $this->assertIsArray($bodyData->shortUrls);

        $this->assertArrayHasKey(0, $bodyData->shortUrls);
        $this->assertArrayHasKey(1, $bodyData->shortUrls);
        $this->assertArrayNotHasKey(2, $bodyData->shortUrls);

        $this->assertEquals(0, $bodyData->shortUrls[0]->id);
        $this->assertEquals("https://www.php.net/manual/de/language.oop5.traits.php", $bodyData->shortUrls[0]->target);
        $this->assertEquals("123.123.123.123", $bodyData->shortUrls[0]->client);
        $this->assertEquals("1", $bodyData->shortUrls[0]->shortId);
    }

    /**
     * Test URL delete API
     *
     * @covers ::deleteUrl
     */
    public function testDeleteUrl(): void
    {
        $req = $this->request("DELETE", "/admin/api/url/123", array(
            "PHP_AUTH_PW" => getenv("ADMIN_KEY")
        ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertEquals("success", $bodyData->status);
    }

    /**
     * Test URL cleanup API
     *
     * @covers ::cleanupUrls
     */
    public function testCleanupUrls(): void
    {
        $req = $this->request("POST", "/admin/api/urlCleanup", array(
            "PHP_AUTH_PW" => getenv("ADMIN_KEY")
        ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertEquals("success", $bodyData->status);
    }

    /**
     * Test user list API
     *
     * @covers ::getUserList
     */
    public function testGetUserList(): void
    {
        $req = $this->request("GET", "/admin/api/user", array(
            "PHP_AUTH_PW" => getenv("ADMIN_KEY")
        ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertEquals("success", $bodyData->status);
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
        $req = $this->request("POST", "/admin/api/user", array(
            "PHP_AUTH_PW" => getenv("ADMIN_KEY")
        ))
            ->withHeader("Content-Type", "application/json")
            ->withParsedBody(array(
                "uniquePin" => "abc876test",
                "displayName" => "User 123"
            ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertEquals("success", $bodyData->status);
    }

    /**
     * Test user delete API
     *
     * @covers ::deleteUser
     */
    public function testDeleteUser(): void
    {
        $req = $this->request("DELETE", "/admin/api/user/5", array(
            "PHP_AUTH_PW" => getenv("ADMIN_KEY")
        ));

        $resp = $this->getApp()->handle($req);

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals("application/json", $resp->getHeaderLine("Content-Type"));

        $bodyData = json_decode((string)$resp->getBody());

        $this->assertEquals("success", $bodyData->status);
    }
}
