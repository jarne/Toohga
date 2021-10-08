<?php
/**
 * Toohga | URL database storage mock
 */

namespace jarne\toohga\tests\storage;

use jarne\toohga\storage\URLStorage;

class URLStorageMock extends URLStorage
{
    /**
     * URLStorageMock constructor.
     */
    public function __construct()
    {
        // empty constructor
    }

    /**
     * @inheritDoc
     */
    public function get(string $id): ?string
    {
        return "https://github.com/jarne/Toohga/blob/master/src/jarne/toohga/api/APIController.php";
    }

    /**
     * @inheritDoc
     */
    public function getAll(): ?array
    {
        return array(
            array(
                "id" => 0,
                "target" => "https://www.php.net/manual/de/language.oop5.traits.php",
                "client" => "123.123.123.123",
                "shortId" => "1"
            ),
            array(
                "id" => 10,
                "target" => "https://odan.github.io/slim4-skeleton/testing.html",
                "client" => "121.121.154.162",
                "shortId" => "b"
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function create(string $ip, string $longUrl, ?int $userId = null): ?string
    {
        return "a";
    }

    /**
     * @inheritDoc
     */
    public function delete(string $id): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function cleanup(): bool
    {
        return true;
    }
}
