<?php
/**
 * Toohga | URL database storage mock
 */

namespace jarne\toohga\tests\storage;

use jarne\toohga\storage\UserStorage;

class UserStorageMock extends UserStorage
{
    public function __construct()
    {
        // empty constructor
    }

    /**
     * @inheritDoc
     */
    public function get(string $uPin): ?int
    {
        return 4;
    }

    /**
     * @inheritDoc
     */
    public function create(string $uniquePin, string $displayName): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): ?array
    {
        return [
            [
                "id" => 0,
                "upin" => "123abc",
                "displayName" => "Test user"
            ],
            [
                "id" => 1,
                "upin" => "456test",
                "displayName" => "Another test user"
            ]
        ];
    }
}
