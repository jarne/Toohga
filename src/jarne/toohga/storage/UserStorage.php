<?php
/**
 * Toohga | User database storage class
 */

namespace jarne\toohga\storage;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

use mysqli;
use mysqli_sql_exception;
use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class UserStorage
{
    /**
     * @var mysqli
     */
    private mysqli $mysqli;

    /**
     * @var RedisAdapter
     */
    private RedisAdapter $redisAdapter;

    /**
     * URLStorage constructor.
     */
    public function __construct()
    {
        $this->initConnections();
    }

    /**
     * Initialize the database and cache connection
     */
    private function initConnections(): void
    {
        try {
            $this->mysqli = new mysqli(
                getenv("MYSQL_HOST"),
                getenv("MYSQL_USER"),
                getenv("MYSQL_PASSWORD"),
                getenv("MYSQL_DATABASE")
            );
        } catch (mysqli_sql_exception $sqlExc) {
            return;
        }

        $redis = new Redis();
        $redis->connect(getenv("REDIS_HOST"));

        $this->redisAdapter = new RedisAdapter($redis);
    }

    /**
     * Get a user by its unique PIN
     *
     * @param string $uPin
     *
     * @return int|null
     */
    public function get(string $uPin): ?int
    {
        try {
            $stmt = $this->mysqli->prepare("SELECT id FROM users WHERE upin = ?");
            $stmt->bind_param("s", $uPin);

            $stmt->execute();

            $res = $stmt->get_result();

            $stmt->close();

            if (($row = $res->fetch_assoc()) === null) {
                return null;
            }

            return $row["id"];
        } catch (mysqli_sql_exception $sqlExc) {
            return null;
        }
    }

    /**
     * Create new user
     *
     * @param string $uniquePin
     * @param string $displayName
     *
     * @return bool
     */
    public function create(string $uniquePin, string $displayName): bool
    {
        try {
            $stmt = $this->mysqli->prepare("INSERT INTO users (upin, displayName) VALUES (?, ?)");
            $stmt->bind_param("ss", $uniquePin, $displayName);

            $res = $stmt->execute();

            $stmt->close();

            return $res;
        } catch (mysqli_sql_exception $sqlExc) {
            return false;
        }
    }

    /**
     * Delete a user by ID
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->mysqli->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);

            $res = $stmt->execute();

            $stmt->close();

            return $res;
        } catch (mysqli_sql_exception $sqlExc) {
            return false;
        }
    }

    /**
     * Get all users
     *
     * @return array|null
     */
    public function getAll(): ?array
    {
        try {
            $stmt = $this->mysqli->prepare("SELECT id, upin, displayName FROM users ORDER BY id LIMIT 10");

            $stmt->execute();

            $res = $stmt->get_result();

            $stmt->close();

            if (($rows = $res->fetch_all(MYSQLI_ASSOC)) === null) {
                return null;
            }

            return $rows;
        } catch (mysqli_sql_exception $sqlExc) {
            return null;
        }
    }
}
