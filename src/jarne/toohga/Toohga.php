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
use Klein\Klein;
use Klein\Request;
use Klein\Response;
use Klein\ServiceProvider;
use Redis;

class Toohga {
    /* @var Klein */
    private $klein;

    /* @var EntityManager */
    private $entityManager;

    public function __construct(Klein $klein) {
        $this->klein = $klein;

        if(class_exists("Dotenv\Dotenv") AND file_exists(__DIR__ . "/../../../.env")) {
            $dotenv = Dotenv::create(__DIR__ . "/../../..");
            $dotenv->load();
        }

        $credentials = array(
            "driver" => "pdo_mysql",
            "host" => getenv("MYSQL_HOST"),
            "user" => getenv("MYSQL_USER"),
            "password" => getenv("MYSQL_PASSWORD"),
            "dbname" => getenv("MYSQL_DATABASE"),
        );

        $redis = new Redis();
        $redis->connect(getenv("REDIS_HOST"));

        $redisCache = new RedisCache();
        $redisCache->setRedis($redis);

        try {
            $this->entityManager = EntityManager::create(
                $credentials,
                Setup::createAnnotationMetadataConfiguration(array("src/jarne/toohga/entity"), false, null, $redisCache)
            );
        } catch(ORMException $exception) {
            exit();
        }
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

            $ip = $req->server()->exists("HTTP_X_REAL_IP") ? $req->server()->get("HTTP_X_REAL_IP") : $req->server()->get("REMOTE_ADDR");

            if(($genId = $this->create($ip, $longUrl)) === null) {
                return $res->json(array(
                    "status" => "failed",
                    "errorCode" => "internal_database_error"
                ));
            }

            $srvHost = $req->server()->get("SERVER_NAME");
            $srvPort = $req->server()->get("SERVER_PORT");

            $portString = "";

            if(
                ($req->isSecure() AND $srvPort !== 443) OR
                (!$req->isSecure() AND $srvPort !== 80)
            ) {
                $portString = ":" . $srvPort;
            }

            $httpPrefix = $req->isSecure() ? "https://" : "http://";

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
        $entityManager = $this->getEntityManager();

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
        $entityManager = $this->getEntityManager();

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
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager {
        return $this->entityManager;
    }

    /**
     * @return Klein
     */
    public function getKlein(): Klein {
        return $this->klein;
    }
}
