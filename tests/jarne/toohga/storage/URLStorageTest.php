<?php

/**
 * Toohga | URL storage tests
 */

namespace jarne\toohga\tests\storage;

use jarne\toohga\storage\URLStorage;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \jarne\toohga\storage\URLStorage
 */
class URLStorageTest extends TestCase
{
    public const TEST_LONG_URL = "https://www.slimframework.com/docs/v3/cookbook/environment.html";

    /**
     * @var URLStorage
     */
    private static URLStorage $storage;

    /**
     * Setup testing environment
     */
    public static function setUpBeforeClass(): void
    {
        self::$storage = new URLStorage();
    }

    /**
     * Test getting an URL entry
     *
     * @covers ::get
     */
    public function testGet(): void
    {
        $long = self::$storage->get(1);

        $this->assertEquals("https://github.com/jarne/Toohga", $long);
    }

    /**
     * Test getting a non-existing URL entry
     *
     * @covers ::get
     */
    public function testGetNotExisting(): void
    {
        $res = self::$storage->get("abc");

        $this->assertNull($res);
    }

    /**
     * Test getting an invalid URL entry
     *
     * @covers ::get
     */
    public function testGetInvalid(): void
    {
        $res = self::$storage->get("-/*");

        $this->assertNull($res);
    }

    /**
     * Test creating a new URL entry
     *
     * @covers ::create
     */
    public function testCreate(): void
    {
        $res = self::$storage->create("123.456.123.456", self::TEST_LONG_URL);

        $this->assertIsString($res);
    }

    /**
     * Test creating a new URL entry and getting this entry
     *
     * @covers ::create
     */
    public function testCreateAndGet(): void
    {
        $short = self::$storage->create("123.456.123.456", self::TEST_LONG_URL);
        $this->assertIsString($short);

        $long = self::$storage->get($short);
        $this->assertEquals(self::TEST_LONG_URL, $long);
    }

    /**
     * Test getting all URL entries
     *
     * @covers ::getAll
     */
    public function testGetAll(): void
    {
        $res = self::$storage->getAll();

        $this->assertIsArray($res);
        $this->assertGreaterThanOrEqual(4, count($res));
    }

    /**
     * Test if created URL entry is present in all URL entries
     *
     * @depends testCreate
     *
     * @covers ::getAll
     */
    public function testGetAllCreated(): void
    {
        $res = self::$storage->getAll();

        $this->assertIsArray($res);
        $this->assertGreaterThanOrEqual(4, count($res));

        $found = false;

        foreach ($res as $entry) {
            if ($entry["target"] === self::TEST_LONG_URL) {
                $found = true;
            }
        }

        $this->assertTrue($found);
    }

    /**
     * Test deleting an URL entry
     *
     * @covers ::delete
     */
    public function testDelete(): void
    {
        $res = self::$storage->delete(2);

        $this->assertTrue($res);
        $this->assertNull(self::$storage->get(2));
    }

    /**
     * Test URL entry cleanup
     *
     * @covers ::cleanup
     */
    public function testCleanup(): void
    {
        $short = self::$storage->create("123.456.123.456", self::TEST_LONG_URL);

        $res = self::$storage->cleanup();

        $this->assertTrue($res);

        $this->assertNull(self::$storage->get(4));
        $this->assertNull(self::$storage->get(3));
        $this->assertNotNull(self::$storage->get($short));

        include "./testinit.php";
    }
}
