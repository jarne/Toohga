<?php
/**
 * Toohga | User storage tests
 */

namespace jarne\toohga\tests\storage;

use jarne\toohga\storage\UserStorage;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \jarne\toohga\storage\UserStorage
 */
class UserStorageTest extends TestCase
{
    /**
     * @var UserStorage
     */
    private static UserStorage $storage;

    /**
     * Setup testing environment
     */
    public static function setUpBeforeClass(): void
    {
        self::$storage = new UserStorage();
    }

    /**
     * Test getting a user
     *
     * @covers ::get
     */
    public function testGet(): void
    {
        $id = self::$storage->get("OjN5Df");

        $this->assertIsInt($id);
    }

    /**
     * Test getting a non-existing user
     *
     * @covers ::get
     */
    public function testGetNotExisting(): void
    {
        $res = self::$storage->get("notexisting789");

        $this->assertNull($res);
    }

    /**
     * Test creating a new user
     *
     * @covers ::create
     */
    public function testCreate(): void
    {
        $res = self::$storage->create("123abc", "Test user");

        $this->assertTrue($res);
    }

    /**
     * Test creating a new user and getting its ID using the unique PIN
     *
     * @covers ::create
     */
    public function testCreateAndGet(): void
    {
        $res = self::$storage->create("456abc", "Second user");
        $this->assertTrue($res);

        $id = self::$storage->get("456abc");
        $this->assertIsInt($id);
    }

    /**
     * Test getting all users
     *
     * @covers ::getAll
     */
    public function testGetAll(): void
    {
        $res = self::$storage->getAll();

        $this->assertIsArray($res);
        $this->assertGreaterThanOrEqual(3, count($res));
    }

    /**
     * Test if created user is present in all users
     *
     * @depends testCreate
     * @covers ::getAll
     */
    public function testGetAllCreated(): void
    {
        $res = self::$storage->getAll();

        $this->assertIsArray($res);
        $this->assertGreaterThanOrEqual(3, count($res));

        $found = false;

        foreach ($res as $entry) {
            if ($entry["displayName"] === "Test user") {
                $found = true;
            }
        }

        $this->assertTrue($found);
    }

    /**
     * Test deleting a user
     *
     * @covers ::delete
     */
    public function testDelete(): void
    {
        $res = self::$storage->delete(3);

        $this->assertTrue($res);
    }

    /**
     * Delete created users from DB
     */
    public static function tearDownAfterClass(): void
    {
        $usersToDelete = ["123abc", "456abc"];

        foreach ($usersToDelete as $user) {
            $uId = self::$storage->get($user);

            if ($uId === null) {
                return;
            }

            self::$storage->delete($uId);
        }
    }
}
