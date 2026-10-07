<?php

namespace App\Controller;

use App\Exception\EmailAlreadyExistsException;
use App\Exception\InvalidCredentialsException;
use App\Service\UserService;
use InvalidArgumentException;

class UserController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function create(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);        

        try {
            $id = $this->userService->createUser($data);
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
        $data = json_decode($json, true);

        try {
            $id = $this->userService->loginUser($data);
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
}