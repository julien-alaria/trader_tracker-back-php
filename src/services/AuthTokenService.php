<?php

namespace TraderTracker\Php\services;

use Firebase\JWT\JWT;

class AuthTokenService {
    public static function generateToken(array $user): string {
        $payload = [
            'id' => $user['id'],
            'role' => $user['role'],
            'analyst_type_id' => $user['analyst_type_id'],
            'iat' => time(),
            'exp' => time() + 3600, //1 hour
        ];

        return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }
}