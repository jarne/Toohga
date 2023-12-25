<?php

/**
 * Toohga | Admin center controller
 */

namespace jarne\toohga\api;

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
    private function tryAuth(ServerRequestInterface $request, ResponseInterface $response): bool|ResponseInterface
    {
        if (!isset($request->getServerParams()["PHP_AUTH_PW"])) {
            return $response->withHeader("WWW-Authenticate", "Basic realm=\"Toohga admin center\"")
                ->withStatus(401);
        }

        $authPw = $request->getServerParams()["PHP_AUTH_PW"];

        if ($authPw !== getenv("ADMIN_KEY")) {
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
        if (($res = $this->tryAuth($request, $response)) !== true) {
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
        if (($res = $this->tryAuth($request, $response)) !== true) {
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
        if (($res = $this->tryAuth($request, $response)) !== true) {
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
        if (($res = $this->tryAuth($request, $response)) !== true) {
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
        if (($res = $this->tryAuth($request, $response)) !== true) {
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
        if (($res = $this->tryAuth($request, $response)) !== true) {
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
        if (($res = $this->tryAuth($request, $response)) !== true) {
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
