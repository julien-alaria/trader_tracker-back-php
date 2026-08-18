<?php

namespace TraderTracker\Php\Utils;

class RequestBody {

    public static function parse(): array {

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            return json_decode(file_get_contents('php://input'), true) ?? [];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $_POST;
        }

        return MultipartParser::parse()['fields'];
    }
}