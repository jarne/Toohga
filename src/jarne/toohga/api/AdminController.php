<?php

/**
 * Toohga | Admin center controller
 */

namespace jarne\toohga\api;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use jarne\toohga\storage\URLStorage;
use jarne\toohga\storage\UserStorage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminController
{
    public const DEFAULT_JWT_ALGO = "HS256";

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
     * Try to authenticate using the admin key
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return bool|ResponseInterface
     */
    private function checkToken(ServerRequestInterface $request, ResponseInterface $response): bool|ResponseInterface
    {
        $authHeaders = $request->getHeader("Authorization");
        if (count($authHeaders) !== 1) {
            return $response->withStatus(401);
        }
        $authHeader = $authHeaders[0];
        $authHeaderParts = explode(" ", $authHeader);

        if (!(count($authHeaderParts) === 2 && $authHeaderParts[0] === "Bearer")) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "request_error"
                    )
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        $suppliedJwt = $authHeaderParts[1];
        try {
            $decoded = JWT::decode($suppliedJwt, new Key(getenv("JWT_SECRET"), self::DEFAULT_JWT_ALGO));
        } catch (Exception $e) {
            return $response->withStatus(401);
        }

        if (!(isset($decoded->admin) && $decoded->admin === true)) {
            return $response->withStatus(401);
        }

        return true;
    }

    /**
     * Show admin center panel
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function panel(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (($res = $this->checkToken($request, $response)) !== true) {
            return $res;
        }

        $view = Twig::fromRequest($request);

        try {
            return $view->render($response, "admin.html.twig");
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            return $response->withStatus(500);
        }
    }

    /**
     * Get an authentication token for the admin API
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function authenticate(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
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

        if (!isset($params["admin_key"])) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "admin_key_missing"
                    )
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        $adminKey = $params["admin_key"];

        if ($adminKey !== getenv("ADMIN_KEY")) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "invalid_credentials"
                    )
                ))
            );
            return $response
                ->withStatus(401)
                ->withHeader("Content-Type", "application/json");
        }

        $issuedAt = time();
        $expires = $issuedAt + 86400; // expires after 24 h

        $payload = [
            "iat" => $issuedAt,
            "exp" => $expires,
            "admin" => true
        ];
        $jwt = JWT::encode($payload, getenv("JWT_SECRET"), self::DEFAULT_JWT_ALGO);

        $response->getBody()->write(
            json_encode(array(
                "jwt" => $jwt,
            ))
        );
        return $response->withHeader("Content-Type", "application/json");
    }

    /**
     * Get the list of the URL entries
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function getUrlList(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        if (($res = $this->checkToken($request, $response)) !== true) {
            return $res;
        }

        $urls = $this->urlStorage->getAll();

        if ($urls === null) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "internal_database_error"
                    )
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        $response->getBody()->write(
            json_encode(array(
                "short_urls" => $urls,
            ))
        );
        return $response->withHeader("Content-Type", "application/json");
    }

    /**
     * Delete an URL entry
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function deleteUrl(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        if (($res = $this->checkToken($request, $response)) !== true) {
            return $res;
        }

        $urlId = $args["urlId"];
        $res = $this->urlStorage->delete($urlId);

        if (!$res) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "internal_database_error"
                    )
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        return $response->withStatus(204);
    }

    /**
     * Cleanup old URLs
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function cleanupUrls(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        if (($res = $this->checkToken($request, $response)) !== true) {
            return $res;
        }

        $res = $this->urlStorage->cleanup();

        if (!$res) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "internal_database_error"
                    )
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        return $response->withStatus(204);
    }

    /**
     * Get the list of users
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function getUserList(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        if (($res = $this->checkToken($request, $response)) !== true) {
            return $res;
        }

        $users = $this->userStorage->getAll();

        if ($users === null) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "internal_database_error"
                    )
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        $response->getBody()->write(
            json_encode(array(
                "users" => $users,
            ))
        );
        return $response->withHeader("Content-Type", "application/json");
    }

    /**
     * Create a user
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function createUser(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        if (($res = $this->checkToken($request, $response)) !== true) {
            return $res;
        }

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

        if (!(isset($params["uniquePin"]) and isset($params["displayName"]))) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "parameters_missing"
                    )
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        $uniquePin = $params["uniquePin"];
        $displayName = $params["displayName"];

        $res = $this->userStorage->create($uniquePin, $displayName);

        if (!$res) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "internal_database_error"
                    )
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        return $response->withStatus(204);
    }

    /**
     * Delete a user
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function deleteUser(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        if (($res = $this->checkToken($request, $response)) !== true) {
            return $res;
        }

        $userId = $args["userId"];
        $res = $this->userStorage->delete($userId);

        if (!$res) {
            $response->getBody()->write(
                json_encode(array(
                    "error" => array(
                        "code" => "internal_database_error"
                    )
                ))
            );
            return $response->withHeader("Content-Type", "application/json");
        }

        return $response->withStatus(204);
    }
}
