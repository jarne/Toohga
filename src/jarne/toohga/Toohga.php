<?php
/**
 * Toohga | main class
 */

namespace jarne\toohga;

use DateTime;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Dotenv\Dotenv;
use Exception;
use jarne\toohga\entity\URL;
use jarne\toohga\service\DecimalConverter;
use Klein\App;
use Klein\Klein;
use Klein\Request;
use Klein\Response;
use Klein\ServiceProvider;
use Redis;

class Toohga {
    /* @var Klein */
    private $klein;

    public function __construct(Klein $klein) {
        $this->klein = $klein;

        if(class_exists("Dotenv\Dotenv") AND file_exists(__DIR__ . "/../../../.env")) {
            $dotenv = Dotenv::create(__DIR__ . "/../../..");
            $dotenv->load();
        }
    }

    /**
     * Initialize the database connection
     */
    public function initDatabase(): void {
        $this->getKlein()->respond(function(Request $req, Response $res, ServiceProvider $service, App $app) {
            $app->register("redCache", function() {
                $redis = new Redis();
                $redis->connect(getenv("REDIS_HOST"));

                $redisCache = new RedisCache();
                $redisCache->setRedis($redis);

                return $redisCache;
            });

            $app->register("dbConn", function() {
                $credentials = array(
                    "driver" => "pdo_mysql",
                    "host" => getenv("MYSQL_HOST"),
                    "user" => getenv("MYSQL_USER"),
                    "password" => getenv("MYSQL_PASSWORD"),
                    "dbname" => getenv("MYSQL_DATABASE"),
                );

                try {
                    $entityManager = EntityManager::create(
                        $credentials,
                        Setup::createAnnotationMetadataConfiguration(array("src/jarne/toohga/entity"), false, null, $this->getRedisCache($this->getKlein()->app()))
                    );
                } catch(ORMException $exception) {
                    return null;
                }

                return $entityManager;
            });
        });
    }

    /**
     * Initialize the URL routes
     */
    public function initRoutes(): void {
        $this->getKlein()->get("/", function(Request $req, Response $res, ServiceProvider $service) {
            $service->render("templates/index.html");
        });

        $this->getKlein()->get("/privacy", function(Request $req, Response $res, ServiceProvider $service) {
            $service->render("templates/privacy.html");
        });

        $this->getKlein()->get("/[:urlId]", function(Request $req, Response $res) {
            $urlId = $req->urlId;

            $longUrl = $this->get($urlId);

            $res->redirect($longUrl);
        });

        $this->getKlein()->post("/", function(Request $req, Response $res) {
            if(($longUrl = $req->param("longUrl")) === null) {
                return $res->json(array(
                    "status" => "failed",
                    "errorCode" => "long_url_parameter_missing"
                ));
            }

            $ip = $req->server()->exists("HTTP_X_REAL_IP") ? $req->server()->get("HTTP_X_REAL_IP") : $req->ip();

            if(($genId = $this->create($ip, $longUrl)) === null) {
                return $res->json(array(
                    "status" => "failed",
                    "errorCode" => "internal_database_error"
                ));
            }

            $srvHost = $req->server()->get("SERVER_NAME");
            $srvPort = $req->server()->get("SERVER_PORT");

            $isSecure = $req->server()->exists("HTTP_X_FORWARDED_PROTO") ? ($req->server()->get("HTTP_X_FORWARDED_PROTO") === "https") : $req->isSecure();

            $portString = "";

            if(
                !$req->server()->exists("HTTP_X_FORWARDED_PROTO") AND
                (
                    ($isSecure AND $srvPort !== 443) OR
                    (!$isSecure AND $srvPort !== 80)
                )
            ) {
                $portString = ":" . $srvPort;
            }

            $httpPrefix = $isSecure ? "https://" : "http://";

            $shortUrl = $httpPrefix . $srvHost . $portString . "/" . $genId;

            return $res->json(array(
                "status" => "success",
                "shortUrl" => $shortUrl,
            ));
        });
    }

    /**
     * Get a shortened URL by ID
     *
     * @param string $id
     * @return string|null
     */
    public function get(string $id): ?string {
        $entityManager = $this->getEntityManager($this->getKlein()->app());

        if(($numberId = DecimalConverter::stringToNumber($id)) !== null) {
            $url = $entityManager->getRepository("jarne\\toohga\\entity\\URL")
                ->find($numberId);

            if($url) {
                return $url->getTarget();
            }
        }

        return null;
    }

    /**
     * Create a new shortened URL in the database
     *
     * @param string $ip
     * @param string $longUrl
     * @return string|null
     * @throws Exception
     */
    public function create(string $ip, string $longUrl): ?string {
        $entityManager = $this->getEntityManager($this->getKlein()->app());

        $url = $entityManager->getRepository("jarne\\toohga\\entity\\URL")
            ->findOneBy(
                array(
                    "target" => $longUrl,
                )
            );

        if(!$url) {
            $url = new URL();
            $url->setCreated(new DateTime());
            $url->setClient($ip);
            $url->setTarget($longUrl);

            try {
                $entityManager->persist($url);
            } catch(ORMException $exception) {
                return null;
            }

            try {
                $entityManager->flush();
            } catch(OptimisticLockException $exception) {
                return null;
            } catch(ORMException $exception) {
                return null;
            }
        }

        return DecimalConverter::numberToString($url->getId());
    }

    /**
     * Get the Redis cache component
     *
     * @param App $app
     * @return RedisCache
     */
    private function getRedisCache(App $app): RedisCache {
        return $app->redCache;
    }

    /**
     * Get the DB entity manager
     *
     * @param App $app
     * @return EntityManager
     */
    private function getEntityManager(App $app): EntityManager {
        return $app->dbConn;
    }

    /**
     * @return Klein
     */
    private function getKlein(): Klein {
        return $this->klein;
    }
}
