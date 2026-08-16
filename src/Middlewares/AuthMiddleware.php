<?php

namespace TraderTracker\Php\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use TraderTracker\Php\Models\UserModel;
use TraderTracker\Php\Utils\AppError;

class AuthMiddleware {

    public static function forRoles(array $roles = []): callable {

        return function (array $params) use ($roles) {
            
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? null;

            $parts = $authHeader ? explode(' ', $authHeader): [null, null];
            [$prefix, $token] = $parts + [null, null];

            if ($prefix !== 'Bearer') {
                throw new AppError("No Bearer token", 401);
            }

            if (!$token) {
                throw new AppError("You must be authenticated to access this ressource", 401);
            }

            try {
                $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            } catch (\Exception $e) {
                throw new AppError("Invalid or expired token", 401);
            }

            if (!isset($decoded->id)) {
                throw new AppError("Invalid Payload", 401);
            }

            $user = UserModel::getUsersById($decoded->id);

            if (!$user) {
                throw new AppError("User not found", 401);
            }

            if (count($roles) > 0 && !in_array($user['role'], $roles, true)) {
                throw new AppError("Permission denied, you are not authorized to access this ressource", 403);
            }

            return ['user' => $user];
        };
    }
}