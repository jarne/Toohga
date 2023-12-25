<?php

/**
 * Toohga | API controller
 */

namespace jarne\toohga\api;

use jarne\toohga\storage\URLStorage;
use jarne\toohga\storage\UserStorage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

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
     * Render home page template
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function home(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);

        return $view->render($response, "index.html.twig", [
            "contactMail" => getenv("CONTACT_EMAIL"),
            "hasPrivacyUrl" => getenv("PRIVACY_URL") !== false,
            "backgroundColors" => $this->getBackgroundColors(getenv("THEME")),
            "analyticsScript" => getenv("ANALYTICS_SCRIPT"),
            "authReq" => getenv("AUTH_REQUIRED") === "true"
        ]);
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
                    "status" => "failed",
                    "errorCode" => "request_error"
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        $userId = null;

        if (getenv("AUTH_REQUIRED") === "true") {
            if (!isset($params["userPin"])) {
                $response->getBody()->write(
                    json_encode(array(
                        "status" => "failed",
                        "errorCode" => "auth_failed"
                    ))
                );
                return $response->withHeader("Content-Type", "application/json");
            }

            $userPin = $params["userPin"];

            $res = $this->userStorage->get($userPin);

            if ($res === null) {
                $response->getBody()->write(
                    json_encode(array(
                        "status" => "failed",
                        "errorCode" => "auth_failed"
                    ))
                );
                return $response->withHeader("Content-Type", "application/json");
            }

            $userId = $res;
        }

        if (!isset($params["longUrl"])) {
            $response->getBody()->write(
                json_encode(array(
                    "status" => "failed",
                    "errorCode" => "long_url_parameter_missing"
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
                    "status" => "failed",
                    "errorCode" => "internal_database_error"
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
                "status" => "success",
                "shortUrl" => $shortUrl,
            ))
        );
        return $response->withHeader("Content-Type", "application/json");
    }

    /**
     * Show the privacy page
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function privacy(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $privacyUrl = getenv("PRIVACY_URL");

        if ($privacyUrl) {
            return $response->withHeader("Location", $privacyUrl)
                ->withStatus(301);
        }

        return $response->withStatus(404);
    }

    /**
     * Return background gradient colors based on selected theme
     *
     * @param string $selectedTheme
     * @return string[]
     */
    private function getBackgroundColors(string $selectedTheme): array
    {
        return match ($selectedTheme) {
            "pink" => ["#fcb5d9", "#f2d5e3"],
            "orange" => ["#fcd194", "#f2e0c6"],
            default => ["#b6f5f9", "#e6f0f2"],
        };
    }
}
