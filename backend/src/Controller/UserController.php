<?php

namespace App\Controller;

use App\Exception\EmailAlreadyExistsException;
use App\Exception\InvalidCredentialsException;
use App\Http\JsonBodyReader;
use App\Service\UserService;
use InvalidArgumentException;

class UserController
{
    private UserService $user_service;
    private JsonBodyReader $json_body_reader;

    public function __construct(UserService $user_service, JsonBodyReader $json_body_reader)
    {
        $this->user_service = $user_service;
        $this->json_body_reader = $json_body_reader;
    }

    public function create(): void
    {
        $json = file_get_contents('php://input');

        try {
            $data = $this->json_body_reader->decodeJson($json);
            $id = $this->user_service->createUser($data);
            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode(['id' => $id]);
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        } catch (EmailAlreadyExistsException $e) {
            http_response_code(409);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function login(): void
    {
        $json = file_get_contents('php://input');
        
        try {
            $data = $this->json_body_reader->decodeJson($json);
            $id = $this->user_service->loginUser($data);
            session_start();
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            http_response_code(204);
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        } catch (InvalidCredentialsException $e) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function logout(): void
    {
        $_SESSION = array();

        /* 
        The destruction of browser cookie before server sessions is intentional so that
        even if the server fails to destroy the session, the browser is logged out.
        */
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        if (session_destroy()) {
            http_response_code(204);
        } else {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'You were unable to logout!']);
        }
    }
}
