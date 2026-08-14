<?php

namespace TraderTracker\Php\Utils;

use Throwable;

class ErrorHandler {

    public static function respond(Throwable $e): void {
        $statusCode = $e instanceof AppError ? $e->getStatusCode() : 500;

        if ($statusCode === 500) {
            error_log("SERVER ERROR: " . $e->getMessage());
        }

        http_response_code($statusCode);
        echo json_encode([
            'message' => $statusCode === 500 ? 'Internal server error' : $e->getMessage(),
        ]);
    }
}