<?php
/**
 * Created by PhpStorm.
 * User: jarne
 * Date: 08.03.17
 * Time: 20:54
 */

namespace jarne\toohga;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use jarne\toohga\entity\URL;
use jarne\toohga\utils\MethodType;

class Toohga {
    /* @var EntityManager */
    private $entityManager;

    public function __construct() {
        $credentials = array(
            "driver" => "pdo_mysql",
            "host" => "tooh.ga",
            "user" => "toohga",
            "password" => "_eI200xm",
            "dbname" => "toohga"
        );

        $this->entityManager = EntityManager::create($credentials, Setup::createAnnotationMetadataConfiguration(array("src/jarne/toohga/entity")));
    }

    /**
     * Process a request
     *
     * @param array $server
     * @param array $post
     * @return string
     */
    public function process(array $server, array $post) {
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
    public function redirect(array $urlParts, string $ip, string $hostname, int $methodType, array $post) {
        switch($methodType) {
            case MethodType::GET:
                if(count($urlParts) == 2) {
                    $this->get($urlParts[1]);
                }
                break;
            case MethodType::POST:
                $this->willReturnJson();

                if(isset($post["longUrl"])) {
                    $longUrl = $post["longUrl"];

                    $id = $this->create($ip, $longUrl);
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
                break;
        }

        return file_get_contents("templates/index.html");
    }

    /**
     * Get a shortened URL by ID
     *
     * @param string $id
     */
    public function get(string $id) {
        $entityManager = $this->getEntityManager();

        if($id != "") {
            $url = $entityManager->getRepository("jarne\\toohga\\entity\\URL")
                ->find($id);

            if($url) {
                $target = $url->getTarget();

                $this->redirectTo($target);
            }
        }
    }

    /**
     * Create a new shortened URL
     *
     * @param string $ip
     * @param string $longUrl
     * @return string
     */
    public function create(string $ip, string $longUrl) {
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
            $entityManager->flush();
        }

        return $url->getId();
    }

    /**
     * Call when the script is going to return JSON
     */
    public function willReturnJson() {
        header("Content-type: application/json");
    }

    /**
     * Redirect the user to an URL
     *
     * @param string $url
     */
    public function redirectTo(string $url) {
        header("Location: " . $url);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager {
        return $this->entityManager;
    }
}