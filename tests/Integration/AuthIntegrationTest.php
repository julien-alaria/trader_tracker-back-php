<?php

namespace TraderTracker\Php\Tests\Integration;

use PHPUnit\Framework\TestCase;

class AuthIntegrationTest extends TestCase
{
    private const BASE_URL = 'http://localhost:8000';

    private function request(string $method, string $path, array $data = [], ?string $token = null): array
    {
        $headers = ["Content-Type: application/json"];
        if ($token) {
            $headers[] = "Authorization: Bearer $token";
        }

        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'content' => json_encode($data),
                'ignore_errors' => true,
            ],
        ]);

        $body = file_get_contents(self::BASE_URL . $path, false, $context);

        preg_match('/\d{3}/', $http_response_header[0] ?? '', $matches);
        $statusCode = (int) ($matches[0] ?? 0);

        return ['status' => $statusCode, 'body' => json_decode($body, true)];
    }

    public function testRegisterThenLoginThenDeleteAccount(): void
    {
        $email = 'integration-' . uniqid() . '@test.com';

        $register = $this->request('POST', '/auth/register', [
            'name' => 'Integration Test',
            'email' => $email,
            'password' => 'Abcdef1!',
        ]);
        $this->assertEquals(201, $register['status']);
        $this->assertArrayHasKey('token', $register['body']);

        $login = $this->request('POST', '/auth/login', [
            'email' => $email,
            'password' => 'Abcdef1!',
        ]);
        $this->assertEquals(200, $login['status']);
        $token = $login['body']['token'];

        $delete = $this->request('DELETE', '/users/me', [], $token);
        $this->assertEquals(200, $delete['status']);
    }
}