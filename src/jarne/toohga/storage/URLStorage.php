<?php

/**
 * Toohga | URL database storage class
 */

namespace jarne\toohga\storage;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

use jarne\toohga\logic\DecimalConverter;
use mysqli;
use mysqli_sql_exception;
use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class URLStorage
{
    public const DEFAULT_DELETE_AFTER_DAYS = 14;

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
     * Get a shortened URL by ID
     *
     * @param string $id
     *
     * @return string|null
     */
    public function get(string $id): ?string
    {
        if (($numberId = DecimalConverter::stringToNumber($id)) === null) {
            return null;
        }

        try {
            $stmt = $this->mysqli->prepare("SELECT target FROM urls WHERE id = ?");
            $stmt->bind_param("i", $numberId);

            $stmt->execute();

            $res = $stmt->get_result();

            $stmt->close();

            if (($row = $res->fetch_assoc()) === null) {
                return null;
            }

            return $row["target"];
        } catch (mysqli_sql_exception $sqlExc) {
            return null;
        }
    }

    /**
     * Get all shortened URLs
     *
     * @return array|null
     */
    public function getAll(): ?array
    {
        try {
            $stmt = $this->mysqli->prepare(
                "SELECT urls.id, created, client, target, displayName FROM urls LEFT JOIN users u on u.id = urls.userId ORDER BY created DESC LIMIT 10"
            );

            $stmt->execute();

            $res = $stmt->get_result();

            $stmt->close();

            if (($rows = $res->fetch_all(MYSQLI_ASSOC)) === null) {
                return null;
            }

            foreach ($rows as $key => $row) {
                $row["shortId"] = DecimalConverter::numberToString($row["id"]);

                $rows[$key] = $row;
            }

            return $rows;
        } catch (mysqli_sql_exception $sqlExc) {
            return null;
        }
    }

    /**
     * Create a new shortened URL in the database
     *
     * @param string $ip
     * @param string $longUrl
     * @param int|null $userId
     *
     * @return string|null
     */
    public function create(string $ip, string $longUrl, ?int $userId = null): ?string
    {
        if (($id = $this->nextId()) === null) {
            return null;
        }

        try {
            $stmt = $this->mysqli->prepare("INSERT INTO urls (id, client, target, userId) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $id, $ip, $longUrl, $userId);

            $res = $stmt->execute();

            $stmt->close();

            if (!$res) {
                return null;
            }

            return DecimalConverter::numberToString($id);
        } catch (mysqli_sql_exception $sqlExc) {
            return null;
        }
    }

    /**
     * Delete a shortened URL by ID
     *
     * @param string $id
     *
     * @return bool
     */
    public function delete(string $id): bool
    {
        if (($numberId = DecimalConverter::stringToNumber($id)) === null) {
            return false;
        }

        try {
            $stmt = $this->mysqli->prepare("DELETE FROM urls WHERE id = ?");
            $stmt->bind_param("i", $numberId);

            $res = $stmt->execute();

            $stmt->close();

            return $res;
        } catch (mysqli_sql_exception $sqlExc) {
            return false;
        }
    }

    /**
     * Delete old, expired URL's
     *
     * @return bool
     */
    public function cleanup(): bool
    {
        $deleteAfterDays = getenv("DELETE_AFTER_DAYS") ?: self::DEFAULT_DELETE_AFTER_DAYS;

        try {
            $stmt = $this->mysqli->prepare("DELETE FROM urls WHERE created < NOW() - INTERVAL ? DAY");
            $stmt->bind_param("i", $deleteAfterDays);

            $res = $stmt->execute();

            $stmt->close();

            return $res;
        } catch (mysqli_sql_exception $sqlExc) {
            return false;
        }
    }

    /**
     * Get the next free DB ID
     *
     * @return int|null
     */
    private function nextId(): ?int
    {
        try {
            $stmt = $this->mysqli->prepare(
                "SELECT IFNULL(min(u1.id + 1), 0) AS nextId FROM urls u1 LEFT JOIN urls u2 ON u1.id + 1 = u2.id WHERE u2.id IS NULL"
            );

            $stmt->execute();

            $res = $stmt->get_result();

            $stmt->close();

            if (($row = $res->fetch_assoc()) === null) {
                return null;
            }

            return intval($row["nextId"]);
        } catch (mysqli_sql_exception $sqlExc) {
            return null;
        }
    }

    /**
     * Check database connection status
     *
     * @return bool
     */
    public function checkConnectionStatus(): bool
    {
        try {
            $stats = $this->mysqli->get_connection_stats();

            return isset($stats["connect_success"]) && $stats["connect_success"] === "1";
        } catch (mysqli_sql_exception $sqlExc) {
            return false;
        }
    }
}
