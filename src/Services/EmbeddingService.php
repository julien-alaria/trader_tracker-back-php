<?php

namespace TraderTracker\Php\Services;

use TraderTracker\Php\Utils\AppError;

class EmbeddingService
{
    public static function embed(string $text): array
    {
        $url = $_ENV['EMBEDDING_URL'] ?? 'http://localhost:11434/api/embed';
        $model = $_ENV['EMBEDDING_MODEL'] ?? 'nomic-embed-text';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode(['model' => $model, 'input' => $text]),
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new AppError(
                'AI assistant unavailable: Ollama is not running or not installed. See README section "AI Assistant Prerequisites".',
                503
            );
        }

        $data = json_decode($response, true);

        if (!isset($data['embeddings'][0])) {
            throw new AppError('Invalid embedding response from Ollama', 500);
        }

        return $data['embeddings'][0];
    }
}