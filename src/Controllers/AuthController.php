<?php

namespace TraderTracker\Php\Controllers;

use Throwable;
use TraderTracker\Php\Models\UserModel;
use TraderTracker\Php\Utils\Sanitizer;
use TraderTracker\Php\Utils\Password;
use TraderTracker\Php\Utils\AppError;
use TraderTracker\Php\Utils\ErrorHandler;
use TraderTracker\Php\Utils\RequestBody;
use TraderTracker\Php\Services\AuthTokenService;

class AuthController {

    public static function login(): void {
        header('Content-Type: application/json');

        try {
            $body = RequestBody::parse();
            $data = Sanitizer::sanitizeLogin($body);

            $user = UserModel::getUsersByEmail($data['email']);

            if ($user === null) {
                throw new AppError("Invalid credentials", 401);
            }

            if (!Password::verify($data['password'], $user['password'])) {
                throw new AppError("Invalid credentials", 401);
            }

            $token = AuthTokenService::generateToken($user);

            http_response_code(200);
            echo json_encode(['message' => 'authorized connection', 'token' => $token]);

        } catch (Throwable $e) {
            ErrorHandler::respond($e);
        }
    }

    public static function register(array $params = []): void {
        header('Content-Type: application/json');

        try {
            $body = RequestBody::parse();
            $sanitizedData = Sanitizer::sanitizeUser($body);

            $sanitizedData['picture'] = $params['picture'] ?? null;
            $sanitizedData['document'] = $params['document'] ?? null;

            $user = UserModel::createUsers($sanitizedData);
            $token = AuthTokenService::generateToken($user);

            http_response_code(201);
            echo json_encode(['user' => $user, 'token' => $token]);

        } catch (Throwable $e) {
            ErrorHandler::respond($e);
        }
    }
}