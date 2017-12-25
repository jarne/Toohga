<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 08.03.17
 * Time: 20:54
 */

namespace jarne\toohga;

use Doctrine\Common\Cache\RedisCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use jarne\toohga\entity\URL;
use jarne\toohga\service\DecimalConverter;
use jarne\toohga\utils\MethodType;

class Toohga {
    /* @var EntityManager */
    private $entityManager;

    public function __construct() {
        $credentials = array(
            "driver" => "pdo_mysql",
            "host" => getenv("MYSQL_HOST"),
            "user" => getenv("MYSQL_USER"),
            "password" => getenv("MYSQL_PASSWORD"),
            "dbname" => getenv("MYSQL_DATABASE")
        );

        $redis = new \Redis();
        $redis->connect(getenv("REDIS_HOST"));

        $redisCache = new RedisCache();
        $redisCache->setRedis($redis);

        try {
            $this->entityManager = EntityManager::create($credentials, Setup::createAnnotationMetadataConfiguration(array("src/jarne/toohga/entity"), false, null, $redisCache));
        } catch(ORMException $exception) {
            exit();
        }
    }

    /**
     * Process a request
     *
     * @param array $server
     * @param array $post
     * @return string
     */
    public function process(array $server, array $post): string {
        $ip = $server["REMOTE_ADDR"];
        $hostname = $server["HTTP_HOST"];
        $uri = $server["REQUEST_URI"];
        $method = $server["REQUEST_METHOD"];

        $urlParts = explode("/", $uri);
        $methodType = ($method == "POST") ? MethodType::POST : MethodType::GET;

        return $this->redirect($urlParts, $ip, $hostname, $methodType, $post);
    }

    /**
     * Get the page where the user wants to land
     *
     * @param array $urlParts
     * @param string $ip
     * @param string $hostname
     * @param int $methodType
     * @param array $post
     * @return string
     */
    public function redirect(array $urlParts, string $ip, string $hostname, int $methodType, array $post): string {
        switch($methodType) {
            case MethodType::GET:
                if(count($urlParts) === 2) {
                    $this->get($urlParts[1]);
                }
                break;
            case MethodType::POST:
                $this->willReturnJson();

                if(isset($post["longUrl"])) {
                    $longUrl = $post["longUrl"];

                    if(($id = $this->create($ip, $longUrl)) !== null) {
                        $shortUrl = "https://" . $hostname . "/" . $id;

                        return(json_encode(array(
                            "status" => "success",
                            "shortUrl" => $shortUrl
                        )));
                    } else {
                        return(json_encode(array(
                            "status" => "failed"
                        )));
                    }
                } else {
                    return(json_encode(array(
                        "status" => "failed"
                    )));
                }
                break;
        }

        return file_get_contents("templates/index.html");
    }

    /**
     * Get a shortened URL by ID
     *
     * @param string $id
     */
    public function get(string $id): void {
        $entityManager = $this->getEntityManager();

        if(($numberId = DecimalConverter::stringToNumber($id)) !== null) {
            $url = $entityManager->getRepository("jarne\\toohga\\entity\\URL")
                ->find($numberId);

            if($url) {
                $this->redirectTo($url->getTarget());
            }
        }
    }

    /**
     * Create a new shortened URL
     *
     * @param string $ip
     * @param string $longUrl
     * @return string|null
     */
    public function create(string $ip, string $longUrl): ?string {
        $entityManager = $this->getEntityManager();

        $url = $entityManager->getRepository("jarne\\toohga\\entity\\URL")
            ->findOneBy(array(
                "target" => $longUrl
            ));

        if(!$url) {
            $url = new URL();
            $url->setCreated(new \DateTime());
            $url->setClient($ip);
            $url->setTarget($longUrl);

            $entityManager->persist($url);

            try {
                $entityManager->flush();
            } catch(OptimisticLockException $exception) {
                return null;
            }
        }

        return DecimalConverter::numberToString($url->getId());
    }

    /**
     * Call when the script is going to return JSON
     */
    public function willReturnJson(): void {
        header("Content-type: application/json");
    }

    /**
     * Redirect the user to an URL
     *
     * @param string $url
     */
    public function redirectTo(string $url): void {
        header("Location: " . $url);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager {
        return $this->entityManager;
    }
}