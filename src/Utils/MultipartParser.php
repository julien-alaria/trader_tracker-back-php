<?php

namespace TraderTracker\Php\Utils;

class MultipartParser
{
    private static ?array $parsed = null;

    public static function parse(): array
    {
        if (self::$parsed !== null) {
            return self::$parsed;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (!preg_match('/boundary=(.*)$/', $contentType, $matches)) {
            return self::$parsed = ['fields' => [], 'files' => []];
        }

        $boundary = trim($matches[1], '"');
        $rawData = file_get_contents('php://input');

        $fields = [];
        $files = [];

        $parts = explode('--' . $boundary, $rawData);

        foreach ($parts as $part) {
            $part = ltrim($part, "\r\n");

            if ($part === '' || $part === '--' || $part === "--\r\n") {
                continue;
            }

            $headerEnd = strpos($part, "\r\n\r\n");
            if ($headerEnd === false) {
                continue;
            }

            $headers = substr($part, 0, $headerEnd);
            $content = substr($part, $headerEnd + 4);
            $content = substr($content, 0, strlen($content) - 2);

            if (!preg_match('/name="([^"]*)"/', $headers, $nameMatch)) {
                continue;
            }
            $fieldName = $nameMatch[1];

            if (preg_match('/filename="([^"]*)"/', $headers, $filenameMatch)) {
                $filename = $filenameMatch[1];

                if ($filename === '') {
                    continue;
                }

                preg_match('/Content-Type:\s*(.*)/i', $headers, $typeMatch);
                $mimeType = isset($typeMatch[1]) ? trim($typeMatch[1]) : 'application/octet-stream';

                $tmpFile = tempnam(sys_get_temp_dir(), 'phpupload_');
                file_put_contents($tmpFile, $content);

                $files[$fieldName] = [
                    'name' => $filename,
                    'type' => $mimeType,
                    'tmp_name' => $tmpFile,
                    'error' => 0,
                    'size' => strlen($content),
                ];
            } else {
                $fields[$fieldName] = $content;
            }
        }

        return self::$parsed = ['fields' => $fields, 'files' => $files];
    }
}