<?php

/**
 * Toohga | API controller
 */

namespace jarne\toohga\api;

use jarne\toohga\storage\URLStorage;
use jarne\toohga\storage\UserStorage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class APIController
{
    /**
     * @var URLStorage
     */
    private URLStorage $urlStorage;

    /**
     * @var UserStorage
     */
    private UserStorage $userStorage;

    /**
     * @param URLStorage $urlStorage
     * @param UserStorage $userStorage
     */
    public function __construct(URLStorage $urlStorage, UserStorage $userStorage)
    {
        $this->urlStorage = $urlStorage;
        $this->userStorage = $userStorage;
    }

    /**
     * Get an URL shortener entry
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $urlId = $args["urlId"];

        $longUrl = $this->urlStorage->get($urlId);

        if ($longUrl !== null) {
            return $response->withHeader("Location", $longUrl)
                ->withStatus(302);
        }

        return $response->withHeader("Location", "/")
            ->withStatus(302);
    }

    /**
     * Create a new short URL entry
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!is_array($params = $request->getParsedBody())) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "request_error"
                    )
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        $userId = null;

        if (getenv("TGA_AUTH_REQUIRED") === "true") {
            if (!isset($params["userPin"])) {
                $response->getBody()->write(
                    json_encode(array(
                        "error" => array(
                            "code" => "auth_failed"
                        )
                    ))
                );
                return $response->withHeader("Content-Type", "application/json");
            }

            $userPin = $params["userPin"];

            $res = $this->userStorage->get($userPin);

            if ($res === null) {
                $response->getBody()->write(
                    json_encode(array(
                        "error" => array(
                            "code" => "auth_failed"
                        )
                    ))
                );
                return $response->withHeader("Content-Type", "application/json");
            }

            $userId = $res;
        }

        if (!isset($params["longUrl"])) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "long_url_parameter_missing"
                    )
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        $longUrl = $params["longUrl"];

        $ip = isset($request->getServerParams()["HTTP_X_REAL_IP"]) ? $request->getServerParams(
        )["HTTP_X_REAL_IP"] : $request->getServerParams()["REMOTE_ADDR"];

        if (($genId = $this->urlStorage->create($ip, $longUrl, $userId)) === null) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "internal_database_error"
                    )
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        $srvHost = $request->getServerParams()["SERVER_NAME"];
        $srvPort = $request->getServerParams()["SERVER_PORT"];

        $isSecure = isset($request->getServerParams()["HTTPS"]) && $request->getServerParams()["HTTPS"] === "on";
        $proxyIsSecure = isset($request->getServerParams()["HTTP_X_FORWARDED_PROTO"]) ? ($request->getServerParams(
        )["HTTP_X_FORWARDED_PROTO"] === "https") : $isSecure;

        $portString = "";

        if (
            !isset($request->getServerParams()["HTTP_X_FORWARDED_PROTO"]) and
            (
                ($proxyIsSecure and $srvPort !== 443) or
                (!$proxyIsSecure and $srvPort !== 80)
            )
        ) {
            $portString = ":" . $srvPort;
        }

        $httpPrefix = $proxyIsSecure ? "https://" : "http://";

        $shortUrl = $httpPrefix . $srvHost . $portString . "/" . $genId;

        $response->getBody()->write(
            json_encode(array(
                "short" => $shortUrl,
            ))
        );
        return $response->withHeader("Content-Type", "application/json");
    }

    /**
     * Get current health status
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function health(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $dbStatus = $this->urlStorage->checkConnectionStatus();

        $response->getBody()->write(
            json_encode(array(
                "database" => $dbStatus ? "ok" : "error",
            ))
        );
        return $response->withHeader("Content-Type", "application/json");
    }
}
